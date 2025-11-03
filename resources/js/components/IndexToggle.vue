<!--
  Nova-Toggle 5 by Almir Hodzic
  Original: https://github.com/almirhodzic/nova-toggle
  Copyright (c) 2025 Almir Hodzic
  MIT License
-->
<template>
    <div class="flex items-center gap-2" @click.stop>
        <!-- Empty placeholder when field is hidden -->
        <div v-if="field.hidden" class="h-5 w-10"></div>

        <!-- Toggle switch with label and help text -->
        <div
            v-else
            class="flex items-center gap-2"
            :class="
                loading || field.readonly ? 'cursor-default' : 'cursor-pointer'
            "
        >
            <!-- Toggle switch wrapper -->
            <label
                class="group disabled:pointer-events-non relative inline-flex h-5 w-10 shrink-0 items-center overflow-hidden rounded-full inset-ring inset-ring-gray-900/5 outline-offset-2 outline-indigo-600 transition-colors duration-200 ease-in-out disabled:opacity-50 has-focus-visible:outline-2"
                :class="labelClasses"
                :style="[{ padding: '2.01px' }, wrapperStyle]"
                @click.stop
            >
                <!-- ON/OFF label text overlay -->
                <span
                    v-if="field.onLabel || field.offLabel"
                    class="pointer-events-none absolute inset-0 flex items-center font-medium tracking-wide uppercase"
                    :style="labelTextStyle"
                    :class="value ? 'justify-start' : 'justify-end'"
                >
                    {{ value ? field.onLabel : field.offLabel }}
                </span>

                <!-- Hidden checkbox input for accessibility -->
                <input
                    type="checkbox"
                    :class="inputClasses"
                    class="absolute inset-0 appearance-none opacity-0 focus:outline-hidden disabled:pointer-events-none"
                    :aria-label="`Toggle ${field.attribute}`"
                    :name="field.attribute"
                    :checked="value"
                    :disabled="loading || field.readonly"
                    @change="handleChange"
                    @click.stop
                />

                <!-- Sliding bullet/circle indicator -->
                <span
                    class="inline-block h-4 w-4 rounded-full shadow-xs ring-1 ring-gray-900/5 transition-transform duration-200 ease-in-out"
                    :style="bulletStyle"
                />
            </label>

            <!-- Optional help text -->
            <p
                v-if="field.helpOnIndex && !field.hidden"
                class="help-text text-xs italic"
            >
                {{ field.helpOnIndex }}
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

/**
 * Field configuration interface
 * Contains all customization options for the toggle field
 */
interface Field {
    value?: boolean;
    attribute: string;
    hidden?: boolean;
    readonly?: boolean;
    onLabel?: string;
    offLabel?: string;
    onLabelColor?: string;
    offLabelColor?: string;
    onLabelColorDark?: string;
    offLabelColorDark?: string;
    onBulletColor?: string;
    offBulletColor?: string;
    onBulletColorDark?: string;
    offBulletColorDark?: string;
    onColor?: string;
    offColor?: string;
    onColorDark?: string;
    offColorDark?: string;
    name?: string;
    helpOnIndex?: string;
    toastShow?: boolean;
    toastLabelKey?: string;
}

/**
 * Component props interface
 */
interface Props {
    field: Field;
    resourceName: string;
    resourceId?: string | number;
    resource?: Record<string, any>;
}

const props = defineProps<Props>();

declare const Nova: any;

// State
const loading = ref(false);
const value = ref<boolean>(props.field.value ?? false);
const isDark = ref(false);

/**
 * Robust resource ID extraction
 * Tries multiple sources to find the resource ID with fallback priority
 */
const id = computed(() => {
    // Priority: resourceId > resource.id.value > resource.id
    if (props.resourceId) return props.resourceId;
    if (props.resource?.id?.value) return props.resource.id.value;
    if (props.resource?.id) return props.resource.id;

    console.error('IndexToggle: No resource ID found', {
        resourceId: props.resourceId,
        resource: props.resource,
        field: props.field,
    });
    return null;
});

let mediaQuery: MediaQueryList | null = null;

/**
 * Dynamic classes for the checkbox input
 * Changes cursor based on loading/readonly state
 */
const inputClasses = computed(() =>
    loading.value || props.field.readonly ? 'cursor-default' : 'cursor-pointer',
);

/**
 * Dynamic classes for the label wrapper
 * Handles disabled state styling
 */
const labelClasses = computed(() => [
    loading.value || props.field.readonly
        ? 'pointer-events-none cursor-default opacity-50'
        : 'cursor-pointer opacity-100',
]);

/**
 * Dynamic styles for ON/OFF label text
 * Adjusts color based on toggle state and color scheme
 */
const labelTextStyle = computed(() => ({
    color: value.value
        ? isDark.value
            ? props.field.onLabelColorDark
            : props.field.onLabelColor
        : isDark.value
          ? props.field.offLabelColorDark
          : props.field.offLabelColor,
    fontSize: '6px',
    padding: '0 6px',
}));

/**
 * Dynamic styles for the sliding bullet
 * Handles color and position based on toggle state
 */
const bulletStyle = computed(() => {
    const onColor = isDark.value
        ? props.field.onBulletColorDark
        : props.field.onBulletColor;
    const offColor = isDark.value
        ? props.field.offBulletColorDark
        : props.field.offBulletColor;

    return {
        backgroundColor: value.value ? onColor : offColor,
        transform: value.value ? 'translateX(1.25rem)' : 'translateX(0)',
    };
});

/**
 * Dynamic styles for the toggle background
 * Changes background color based on toggle state and color scheme
 */
const wrapperStyle = computed(() => {
    const onColor = isDark.value
        ? props.field.onColorDark
        : props.field.onColor;
    const offColor = isDark.value
        ? props.field.offColorDark
        : props.field.offColor;

    return {
        backgroundColor: value.value ? onColor : offColor,
    };
});

/**
 * Handle toggle state change
 * Sends AJAX request to update the resource attribute and handles response
 */
const handleChange = async () => {
    if (props.field.readonly || loading.value) {
        return;
    }

    // Validate resource ID before making request
    if (!id.value) {
        Nova.$toasted?.show('Error: Resource ID not found', { type: 'error' });
        console.error('Cannot toggle: No resource ID', props);
        return;
    }

    // Optimistic UI update
    const prev = value.value;
    value.value = !prev;
    loading.value = true;

    try {
        const url = `/nova-vendor/nova-toggle/toggle/${props.resourceName}/${id.value}`;

        const res = await Nova.request().post(url, {
            attribute: props.field.attribute,
            labelKey: props.field.toastLabelKey,
        });

        if (res.data?.success) {
            // Update with server response
            value.value = !!res.data.value;

            // Show success toast notification if enabled
            if (props.field.toastShow !== false) {
                const getToastMessage = () => {
                    const action = value.value ? 'activated' : 'deactivated';
                    const label = res.data.label || props.field.name;
                    return `"${label}" ${action}!`;
                };

                Nova.$toasted?.show(getToastMessage(), { type: 'success' });
            }
        } else {
            throw new Error('Invalid response from server');
        }
    } catch (e: any) {
        console.error('Toggle error:', e);

        // Revert to previous value on error
        value.value = prev;

        const errorMessage =
            e.response?.data?.message || e.message || 'Error occurred.';
        Nova.$toasted?.show(errorMessage, { type: 'error' });
    } finally {
        loading.value = false;
    }
};

/**
 * Handle system color scheme changes
 * Updates dark mode state when user switches system theme
 */
const handleColorSchemeChange = (e: MediaQueryListEvent) => {
    isDark.value = e.matches;
};

/**
 * Initialize component on mount
 * Sets up color scheme detection and checks for pending toast messages
 */
onMounted(() => {
    // Check for pending toast message from session storage
    const message = sessionStorage.getItem('nova-toggle-message');
    if (message) {
        sessionStorage.removeItem('nova-toggle-message');
        Nova.$toasted?.show(message, { type: 'success', duration: 3000 });
    }

    // Initialize dark mode detection
    mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    isDark.value = mediaQuery.matches;
    mediaQuery.addEventListener('change', handleColorSchemeChange);
});

/**
 * Cleanup on component unmount
 * Removes color scheme change listener
 */
onBeforeUnmount(() => {
    mediaQuery?.removeEventListener('change', handleColorSchemeChange);
});
</script>

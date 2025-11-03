<!--
  Nova-Toggle 5 by Almir Hodzic
  Original: https://github.com/almirhodzic/nova-toggle
  Copyright (c) 2025 Almir Hodzic
  MIT License
-->
<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <div class="flex items-center gap-2">
                <label
                    class="group pointer-events-none relative inline-flex h-5 w-10 shrink-0 items-center overflow-hidden rounded-full opacity-50 inset-ring inset-ring-gray-900/5 outline-offset-2 outline-indigo-600 transition-colors duration-200 ease-in-out"
                    :style="[{ padding: '2.25px' }, wrapperStyle]"
                >
                    <span
                        v-if="field.onLabel || field.offLabel"
                        class="pointer-events-none absolute inset-0 flex items-center font-medium tracking-wide uppercase"
                        :style="labelTextStyle"
                        :class="value ? 'justify-start' : 'justify-end'"
                    >
                        {{ value ? field.onLabel : field.offLabel }}
                    </span>

                    <span
                        class="inline-block h-4 w-4 rounded-full shadow-xs ring-1 ring-gray-900/5 transition-transform duration-200 ease-in-out"
                        :style="bulletStyle"
                    />
                </label>

                <p
                    v-if="field.helpOnDetail && !field.hidden"
                    class="help-text text-xs italic"
                >
                    {{ field.helpOnDetail }}
                </p>
            </div>
        </template>
    </PanelItem>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

interface Field {
    value?: boolean;
    hidden?: boolean;
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
    helpOnDetail?: string;
}

interface Props {
    index?: number;
    field: Field;
    resource?: Record<string, any>;
    resourceName?: string;
    resourceId?: string | number;
}

const props = defineProps<Props>();

const value = ref<boolean>(props.field.value ?? false);
const isDark = ref(false);

let mediaQuery: MediaQueryList | null = null;

// Computed
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

// Lifecycle
const handleColorSchemeChange = (e: MediaQueryListEvent) => {
    isDark.value = e.matches;
};

onMounted(() => {
    mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    isDark.value = mediaQuery.matches;
    mediaQuery.addEventListener('change', handleColorSchemeChange);
});

onBeforeUnmount(() => {
    mediaQuery?.removeEventListener('change', handleColorSchemeChange);
});
</script>

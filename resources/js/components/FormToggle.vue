<!--
  Nova-Toggle 5 by Almir Hodzic
  Original: https://github.com/almirhodzic/nova-toggle
  Copyright (c) 2025 Almir Hodzic
  MIT License
-->
<template>
    <DefaultField
        :field="currentField"
        :errors="errors"
        :full-width-content="fullWidthContent"
    >
        <template #field>
            <div class="mt-1 flex items-center gap-2">
                <label
                    class="group relative inline-flex h-5 w-10 shrink-0 items-center overflow-hidden rounded-full inset-ring inset-ring-gray-900/5 outline-offset-2 outline-indigo-600 transition-colors duration-200 ease-in-out has-focus-visible:outline-2"
                    :class="labelClasses"
                    :style="[{ padding: '2.01px' }, wrapperStyle]"
                    @click="toggle"
                >
                    <span
                        v-if="currentField?.onLabel || currentField?.offLabel"
                        class="pointer-events-none absolute inset-0 flex items-center px-1.5 font-medium tracking-wide uppercase"
                        :style="{ ...labelTextStyle, fontSize: '6px' }"
                        :class="checked ? 'justify-start' : 'justify-end'"
                    >
                        {{
                            checked
                                ? currentField?.onLabel
                                : currentField?.offLabel
                        }}
                    </span>
                    <span
                        class="inline-block h-4 w-4 rounded-full shadow-xs ring-1 ring-gray-900/5 transition-transform duration-200 ease-in-out"
                        :class="bulletClasses"
                        :style="bulletStyle"
                    />
                </label>
                <p
                    v-if="currentField?.helpOnForm"
                    class="help-text text-xs italic"
                >
                    {{ currentField.helpOnForm }}
                </p>
            </div>
        </template>
    </DefaultField>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { useFormToggle } from '../composables/useFormToggle';

interface Props {
    resourceName?: string;
    resourceId?: string | number;
    field: object;
    errors?: object;
    showHelpText?: boolean;
    fullWidthContent?: boolean;
}

defineProps<Props>();

const { currentField, value, loading, currentlyIsReadonly, setInitialValue } =
    useFormToggle();

const isDark = ref(false);
let mediaQuery: MediaQueryList | null = null;

const toggle = () => {
    if (!currentlyIsReadonly.value) {
        value.value = !value.value;
    }
};

const checked = computed(() => Boolean(value.value));

const labelClasses = computed(() => [
    loading.value || currentField.value?.readonly
        ? 'pointer-events-none cursor-default opacity-50'
        : 'cursor-pointer opacity-100',
]);

const bulletClasses = computed(() => [
    loading.value || currentField.value?.readonly
        ? 'pointer-events-none'
        : 'cursor-pointer',
]);

const labelTextStyle = computed(() => ({
    color: value.value
        ? isDark.value
            ? currentField.value?.onLabelColorDark
            : currentField.value?.onLabelColor
        : isDark.value
          ? currentField.value?.offLabelColorDark
          : currentField.value?.offLabelColor,
}));

const bulletStyle = computed(() => {
    const onColor = isDark.value
        ? currentField.value?.onBulletColorDark
        : currentField.value?.onBulletColor;
    const offColor = isDark.value
        ? currentField.value?.offBulletColorDark
        : currentField.value?.offBulletColor;

    return {
        backgroundColor: checked.value ? onColor : offColor,
        transform: checked.value ? 'translateX(1.25rem)' : 'translateX(0)',
    };
});

const wrapperStyle = computed(() => {
    const onColor = isDark.value
        ? currentField.value?.onColorDark
        : currentField.value?.onColor;
    const offColor = isDark.value
        ? currentField.value?.offColorDark
        : currentField.value?.offColor;

    return {
        backgroundColor: checked.value ? onColor : offColor,
    };
});

const handleColorSchemeChange = (e: MediaQueryListEvent) => {
    isDark.value = e.matches;
};

onMounted(() => {
    setInitialValue();

    mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    isDark.value = mediaQuery.matches;
    mediaQuery.addEventListener('change', handleColorSchemeChange);
});

onBeforeUnmount(() => {
    mediaQuery?.removeEventListener('change', handleColorSchemeChange);
});
</script>

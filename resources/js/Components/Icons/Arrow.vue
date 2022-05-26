<template>
    <div
        :class="{
            'flex w-fit': true,
            'rotate-0': direction === 'right',
            'rotate-90': direction === 'down',
            'rotate-180': direction === 'left',
            '-rotate-90': direction === 'up',
        }"
    >
        <svg
            id="arrow"
            xmlns="http://www.w3.org/2000/svg"
            width="11.949"
            height="21.668"
            viewBox="0 0 11.949 21.668"
        >
            <rect
                width="1.5"
                height="15"
                rx="0.75"
                transform="translate(1.061 21.668) rotate(-135)"
                fill="#0074c8"
            />
            <rect
                width="1.5"
                height="15"
                rx="0.75"
                transform="translate(0.281 1.061) rotate(-45)"
                fill="#0074c8"
            />
        </svg>
    </div>
</template>
<script setup>
const props = defineProps({
    direction: {
        type: String,
        default: "right",
    },
});
</script>

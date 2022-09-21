<template>
    <svg
        id="Component_351_10"
        data-name="Component 351 – 10"
        xmlns="http://www.w3.org/2000/svg"
        width="30.549"
        height="60.891"
        viewBox="0 0 30.549 60.891"
    >
        <path
            id="Path_3326"
            data-name="Path 3326"
            d="M37.93,6.609H16.051v40H46.6V15.3Zm7.423,38.678H17.1V7.785H35.665V17.67h9.688Z"
            transform="translate(-16.051 -6.609)"
            :class="{
                'fill-secondary': active,
                'fill-base-content': !active,
            }"
        />
        <text
            id="sms"
            transform="translate(15.275 57.891)"
            font-size="13"
            font-family="Muli-Regular, Muli"
            :class="{
                'fill-secondary': active,
                'fill-base-content': !active,
            }"
        >
            <tspan x="-12.161" y="0">sms</tspan>
        </text>
        <path
            id="comm"
            d="M5.463,21.494a2.446,2.446,0,0,0,2.443,2.443H9.6a.6.6,0,0,1,.6.6v3.9l2.121-2.121,2.2-2.2a.6.6,0,0,1,.422-.175h5.721A2.446,2.446,0,0,0,23.1,21.494V14.021a2.446,2.446,0,0,0-2.443-2.443H7.908a2.446,2.446,0,0,0-2.443,2.443Z"
            transform="translate(0.992 5.867)"
            fill="#fff"
        />
    </svg>
</template>

<script setup>
const props = defineProps({
    active: {
        type: Boolean,
        default: false,
    },
});
</script>

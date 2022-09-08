<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink"
        width="34"
        height="60.891"
        viewBox="0 0 34 60.891"
    >
        <defs>
            <clipPath :id="id_clip">
                <rect
                    id="Rectangle_2530"
                    data-name="Rectangle 2530"
                    width="18.736"
                    height="14.246"
                    fill="#fff"
                />
            </clipPath>
        </defs>
        <g
            id="Component_351_9"
            data-name="Component 351 – 9"
            transform="translate(1)"
        >
            <path
                id="Path_3326"
                data-name="Path 3326"
                d="M37.93,6.609H16.051v40H46.6V15.3Zm7.423,38.678H17.1V7.785H35.665V17.67h9.688Z"
                transform="translate(-15.326 -6.609)"
                :class="{
                    'fill-secondary': active,
                    'fill-base-content': !active,
                }"
            />
            <text
                id="email"
                transform="translate(16 57.891)"
                :class="{
                    'fill-secondary': active,
                    'fill-base-content': !active,
                }"
                font-size="13"
                font-family="Muli-Regular, Muli"
            >
                <tspan x="-16.243" y="0">email</tspan>
            </text>
            <g
                id="Group_2576"
                data-name="Group 2576"
                transform="translate(6.632 17.075)"
            >
                <g
                    id="Group_1539"
                    data-name="Group 1539"
                    :clip-path="`url(#${id_clip})`"
                >
                    <path
                        id="Path_3061"
                        data-name="Path 3061"
                        d="M18.274,0H.461A.461.461,0,0,0,0,.461V13.784a.461.461,0,0,0,.461.461H18.274a.461.461,0,0,0,.461-.461V.461A.461.461,0,0,0,18.274,0M17.1,1.123S9.37,8.845,9.367,8.845L1.65,1.123Zm.36,12H1.28a.222.222,0,0,1-.222-.222V2.1L8.383,9.424a1.378,1.378,0,0,0,.99.414,1.346,1.346,0,0,0,.982-.416L17.678,2.1V12.9a.222.222,0,0,1-.222.222"
                        fill="#fff"
                    />
                </g>
            </g>
        </g>
    </svg>
</template>

<script setup>
import { onMounted, ref } from "vue";

const props = defineProps({
    active: {
        type: Boolean,
        default: false,
    },
});

const id_clip = ref("clip-path");

const genId = () => {
    return String(Date.now().toString(32) + Math.random().toString(16)).replace(
        /\./g,
        ""
    );
};

onMounted(() => {
    let id = genId();
    id_clip.value = id;
});
</script>

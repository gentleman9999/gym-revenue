<template>
    <svg
        id="Component_351_11"
        data-name="Component 351 – 11"
        xmlns="http://www.w3.org/2000/svg"
        xmlns:xlink="http://www.w3.org/1999/xlink"
        width="30.549"
        height="60.891"
        viewBox="0 0 30.549 60.891"
    >
        <defs>
            <clipPath :id="id_clip">
                <rect
                    id="Rectangle_2522"
                    data-name="Rectangle 2522"
                    width="16.245"
                    height="16.245"
                    fill="#fff"
                />
            </clipPath>
        </defs>
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
            id="call"
            transform="translate(15.275 57.891)"
            font-size="13"
            font-family="Muli-Regular, Muli"
            :class="{
                'fill-secondary': active,
                'fill-base-content': !active,
            }"
        >
            <tspan x="-10.212" y="0">call</tspan>
        </text>
        <g
            id="Group_2577"
            data-name="Group 2577"
            transform="translate(7.152 17.719)"
        >
            <g
                id="Group_1510"
                data-name="Group 1510"
                :clip-path="`url(#${id_clip})`"
            >
                <path
                    id="Path_3048"
                    data-name="Path 3048"
                    d="M100.15,36.5a.594.594,0,0,1-.594-.594,4.023,4.023,0,0,0-4.018-4.018.594.594,0,1,1,0-1.189,5.213,5.213,0,0,1,5.207,5.207.594.594,0,0,1-.594.594"
                    transform="translate(-87.051 -28.151)"
                    fill="#fff"
                />
                <path
                    id="Path_3049"
                    data-name="Path 3049"
                    d="M102.439,11.263a.594.594,0,0,1-.594-.594,6.313,6.313,0,0,0-6.306-6.306.594.594,0,0,1,0-1.189,7.5,7.5,0,0,1,7.5,7.495.594.594,0,0,1-.594.594"
                    transform="translate(-87.052 -2.91)"
                    fill="#fff"
                />
                <path
                    id="Path_3050"
                    data-name="Path 3050"
                    d="M1.094,1h0c-2.253,2.253-.911,7.246,3,11.153s8.9,5.248,11.153,3a5.387,5.387,0,0,0,.993-2.234.582.582,0,0,0-.331-.634l-4.09-1.635a.776.776,0,0,0-.766.173L9.864,12.008a.731.731,0,0,1-.81.166l-.06-.032-.026-.015a12.328,12.328,0,0,1-2.75-2.1,12.33,12.33,0,0,1-2.1-2.749L4.1,7.253,4.07,7.191a.731.731,0,0,1,.166-.81L5.422,5.195A.776.776,0,0,0,5.6,4.429L3.961.34A.582.582,0,0,0,3.328.009,5.387,5.387,0,0,0,1.094,1"
                    transform="translate(0 0.001)"
                    fill="#fff"
                />
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

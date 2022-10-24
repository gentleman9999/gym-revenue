<template>
    <li class="even:bg-black even:bg-opacity-10">
        <div
            role="button"
            @click="$emit('selected', file.url)"
            class="flex items-center px-4 hover:bg-secondary hover:bg-opacity-10 transition-all cursor-pointer"
        >
            <div class="w-[5rem] p-2">
                <img :src="file.url" />
            </div>
            <div class="flex flex-col max-w-[16rem]">
                <p
                    class="max-w-[100%] whitespace-nowrap overflow-hidden text-ellipsis"
                >
                    {{ file.filename }}
                </p>
                <p
                    class="italic overflow-hidden text-ellipsis text-primary-content text-opacity-20"
                >
                    {{ formatBytes(file.size) }}
                </p>
            </div>
        </div>
    </li>
</template>

<script setup>
import { ref } from "vue";
import { formatBytes } from "@/Pages/MassCommunication/components/Creator/helpers";

const props = defineProps({
    file: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <jet-bar-container>
        <div class="bg-base-200 w-full rounded-lg p-4">
            <div class="flex flex-row items-center mb-4">
                <h2 class="font-semibold text-xl leading-tight">{{ title }}</h2>
                <div class="flex-grow" />
                <div
                    class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                ></div>
            </div>

            <div class="flex flex-row items-center mb-4" v-if="links.length > 0">
                <div class="hidden space-x-8 sm:-my-px sm:flex pt-6">
                    <a
                        v-for="{ label, href, onClick, active} in links"
                        class="inline-flex items-center border-b-2 border-transparent text-sm font-medium leading-5 hover:border-base-100-300 focus:outline-none focus:border-base-100-300 transition"
                        :class="(active) ? ' outline rounded-full px-4': ''"
                        :href="href"
                        @click="onClick"
                    >
                        {{ label }}
                    </a>
                </div>

                <div class="flex-grow" />
            </div>
        </div>
    </jet-bar-container>
</template>
<script>
import { defineComponent } from "vue";
import JetBarContainer from "@/Components/JetBarContainer";

export default defineComponent({
    components: {JetBarContainer},
    props: {
        links: {
            type: Array,
            default: [],
        },
        title: {
            type: String,
            required: true,
        },
    },
});
</script>

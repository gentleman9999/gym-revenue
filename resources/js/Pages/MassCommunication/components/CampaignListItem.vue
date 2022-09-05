<template>
    <tr class="border-t border-b border-secondary">
        <td class="text-xl" colspan="4">
            <div class="border-r-2 border-secondary">
                {{ campaign.name }}
            </div>
        </td>
        <td class="text-center text-2xl font-bold text-secondary">
            <div class="border-r-2 border-secondary">44</div>
        </td>
        <td class="text-center text-2xl font-bold text-secondary">
            <div class="border-r-2 border-secondary">22</div>
        </td>
        <td class="text-center text-2xl font-bold text-secondary">
            <div class="border-r-2 border-secondary">88</div>
        </td>
        <td colspan="2" class="text-center">
            <div class="border-r-2 border-secondary">
                <div class="mr-2 inline-block">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 16 16"
                    >
                        <path
                            id="Path_96"
                            data-name="Path 96"
                            d="M9,0H7A.945.945,0,0,0,6,1V15a.945.945,0,0,0,1,1H9a.945.945,0,0,0,1-1V1A.945.945,0,0,0,9,0Z"
                            fill="#82d300"
                        />
                        <path
                            id="Path_97"
                            data-name="Path 97"
                            d="M3,10H1a.945.945,0,0,0-1,1v4a.945.945,0,0,0,1,1H3a.945.945,0,0,0,1-1V11A.945.945,0,0,0,3,10Z"
                            fill="#82d300"
                        />
                        <path
                            id="Path_98"
                            data-name="Path 98"
                            d="M15,5H13a.945.945,0,0,0-1,1v9a.945.945,0,0,0,1,1h2a.945.945,0,0,0,1-1V6A.945.945,0,0,0,15,5Z"
                            fill="#82d300"
                        />
                    </svg>
                </div>

                <button
                    @click="$emit('view', campaign, campaignType)"
                    class="btn btn-secondary btn-sm normal-case"
                >
                    View
                </button>
            </div>
        </td>
        <td colspan="2" class="text-center">
            <div>
                <button class="btn btn-secondary btn-sm normal-case">
                    In Progress
                    <div class="ml-2 inline-block h-3 w-3 fill-base-content">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 448 512"
                        >
                            <path
                                d="M224 416c-8.188 0-16.38-3.125-22.62-9.375l-192-192c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L224 338.8l169.4-169.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-192 192C240.4 412.9 232.2 416 224 416z"
                            />
                        </svg>
                    </div>
                </button>
            </div>
        </td>
    </tr>
</template>

<style scoped>
td {
    @apply py-4;
}
</style>

<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    campaign: {
        type: Object,
        default: {},
    },
    campaignType: {
        type: String,
        required: true,
    },
});
</script>

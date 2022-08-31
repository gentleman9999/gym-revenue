<template>
    <div
        class="flex flex-col md:flex-row w-full justify-between mt-[-14px] mb-[51px] space-x-3"
    >
        <slot name="title"></slot>
        <div class="reports-title-filter">
            <div class="flex flex-row items-center space-x-3">
                <div class="md:ml-[0.75rem]">
                    <select-box
                        label="Data View"
                        size="xs"
                        :items="viewOptions"
                        :value="viewMode"
                        :onChange="setViewMode"
                    />
                </div>
                <Button size="xs" secondary class="mt-1"> + Add Lead </Button>
            </div>
            <div class="flex flex-row items-center space-x-3">
                <Button outline size="xs" secondary>
                    + Build a Campaign
                </Button>
                <Button outline size="xs" secondary> + Communication </Button>
            </div>
            <search-input placeholder="Search" size="xs" />
        </div>
    </div>
</template>
<style scoped>
.reports-title-filter .btn-secondary {
    @apply text-base-content border-secondary rounded;
}
@media (max-width: 480px) {
    .reports-title-filter {
        width: calc(100vw - 32px);
    }
}
.reports-title-filter {
    max-width: calc(100vw - 32px);
    @apply flex md:flex-row md:space-x-3 md:space-y-1 space-y-3 md:items-center flex-col flex-wrap lg:flex-nowrap;
}
</style>
<script setup>
import { ref } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import SearchInput from "@/Components/SearchInput.vue";
import Button from "@/Components/Button.vue";
import SelectBox from "@/Components/SelectBox";
const props = defineProps({
    title: {
        type: String,
        default: "",
    },
});
const viewOptions = ["Yesterday", "Today", "Week", "MTD"];

const viewMode = ref("");
const setViewMode = (value) => {
    viewMode.value = value;
};
</script>

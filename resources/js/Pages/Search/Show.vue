<template>
    <LayoutHeader title="Search" />
    <page-toolbar-nav title="Global Search" />
    <div class="max-w-8xl">
        <div class="flex justify-between h-16">
            <div class="flex m-auto">
                <global-search
                    class="z-10"
                    placeholder="Search"
                    id="term"
                    name="term"
                    :initial-value="term"
                    :animate="false"
                />
            </div>
        </div>
    </div>
    <search-result :results="results" />
</template>
<script setup>
import { defineComponent, ref } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import GlobalSearch from "@/Components/GlobalSearch.vue";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm.vue";
import Button from "@/Components/Button.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import PageToolbarNav from "@/Components/PageToolbarNav.vue";
import SearchResult from "./components/SearchResult/index.vue";
const props = defineProps({
    term: {
        type: String,
        default: "",
    },
    results: {
        type: Object,
    },
});
const fields = ["name"];
const results = props.results;
</script>

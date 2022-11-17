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
                    @change="term = $event"
                />
            </div>
        </div>
    </div>
    <search-result :results="data" @update-page="page = $event" />
</template>
<script setup>
import { computed, ref } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import GlobalSearch from "@/Components/GlobalSearch.vue";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm.vue";
import Button from "@/Components/Button.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import PageToolbarNav from "@/Components/PageToolbarNav.vue";
import SearchResult from "./components/SearchResult/index.vue";
import { useQuery } from "@vue/apollo-composable";
import queries from "@/gql/queries";

const term = ref("");
const page = ref(1);

const { result } = useQuery(
    queries["global_search"],
    {
        term: term,
        pagination: {
            limit: 10,
            page: page,
        },
    },
    {
        throttle: 500,
    }
);

const data = computed(
    () =>
        result.value?.globalSearch ?? {
            data: [],
            paginatorInfo: {},
        }
);
</script>

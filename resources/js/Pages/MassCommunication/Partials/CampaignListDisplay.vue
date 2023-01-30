<template>
    <template v-if="(isLoading || modelLoading) && !result">
        <daisy-modal :closable="false" :open="true" :showCloseButton="false">
            <Spinner />
        </daisy-modal>
    </template>

    <template v-else-if="!isLoading && !!resources">
        <div class="flex flex-col">
            <div class="campaign-wrapper">
                <div class="campaign-title">{{ currentFilters.listOne }}</div>
                <div class="campaign-body flex-col">
                    <campaign-list
                        :campaigns="formatForStatus(resources.data)"
                        :type="type"
                        :filter="currentFilters.listOne"
                        @open-campaign="
                            ({ type, campaign }) =>
                                $emit('edit', type, campaign?.id)
                        "
                    />
                </div>
            </div>
            <div class="campaign-wrapper">
                <div class="campaign-title">{{ currentFilters.listTwo }}</div>
                <div class="campaign-body flex-col">
                    <campaign-list
                        :campaigns="formatForStatus(resources.data)"
                        :filter="currentFilters.listTwo"
                        @open-campaign="
                            ({ type, campaign }) =>
                                $emit('edit', type, campaign?.id)
                        "
                        :type="type"
                    />
                </div>
            </div>
            <div class="campaign-wrapper">
                <div class="campaign-title">Recent campaigns</div>
                <div class="campaign-body flex-col">
                    <recent-campaign />
                </div>
            </div>
            <div class="text-secondary text-lg pt-6 pl-6 cursor-pointer">
                Load all activity
            </div>
        </div>
    </template>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import queries from "@/gql/queries";
import { useQuery } from "@vue/apollo-composable";
import CampaignList from "../components/CampaignList/CampaignList.vue";
import RecentCampaign from "../components/RecentCampaign/RecentCampaign.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import Spinner from "@/Components/Spinner.vue";
import { FILTER } from "@/Pages/MassCommunication/components/Creator/helpers";

const emit = defineEmits(["edit", "reload"]);

const props = defineProps({
    type: {
        type: String,
        required: true,
    },
});

const param = ref({
    page: 1,
});

const {
    result,
    loading: modelLoading,
    error,
    refetch,
} = useQuery(
    queries[props.type + "Campaigns"],
    props.param ? props.param : param,
    {
        throttle: 500,
    }
);

const resources = computed(() => {
    if (result.value && result.value[props.type + "Campaigns"]) {
        return _.cloneDeep(result.value[props.type + "Campaigns"]);
    } else return null;
});

const formatForStatus = (data) => {
    if (!data instanceof Array) return [];
    return data.map((p) => ({
        ...p,
        status: p["status"]?.value,
    }));
};

const isLoading = ref(true);
const currentFilters = ref({
    listOne: FILTER["CURRENT"],
    listTwo: FILTER["DRAFT"],
});

watch(modelLoading, (nv, ov) => {
    console.log("resources:", resources);
    if (!!resources?.value) {
        isLoading.value = false;
    }
});
</script>

<style scoped>
.campaign-wrapper {
    @apply flex flex-col pt-12;
}
.campaign-title {
    @apply pb-4 text-xl font-bold text-base-content capitalize;
}

.campaign-body {
    @apply flex border border-secondary rounded p-4 bg-neutral;
}
</style>

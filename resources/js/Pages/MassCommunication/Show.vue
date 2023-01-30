<template>
    <LayoutHeader title="Mass Communications Dashboard"> </LayoutHeader>

    <teleport to="#premaincontent">
        <ToolBar
            :toggleDripBuilder="() => handleNewCampaign('drip')"
            :toggleScheduleBuilder="handleNewCampaign"
        />
    </teleport>

    <jet-bar-container>
        <div class="flex flex-wrap gap-2 justify-between">
            <h1 class="text-2xl">Mass Comms Dashboard</h1>
            <div class="flex gap-2">
                <button
                    @click="handleClickTabSchedule"
                    class="btn btn-outline btn-secondary border-secondary btn-sm !text-base-content !normal-case"
                    :class="{ '!btn-active': selectedTab === 'scheduled' }"
                >
                    Scheduled Campaigns
                </button>
                <button
                    @click="handleClickTabDrip"
                    class="btn btn-outline btn-secondary btn-sm !text-base-content !normal-case"
                    :class="{ '!btn-active': selectedTab === 'drip' }"
                >
                    Drip Campaigns
                </button>
            </div>
        </div>

        <CampaignListDisplay
            v-if="selectedTab === 'scheduled'"
            @edit="handleEditCampaign"
            type="scheduled"
        />

        <CampaignListDisplay
            v-if="selectedTab === 'drip'"
            @edit="handleEditCampaign"
            type="drip"
        />
    </jet-bar-container>

    <!-- DRIP CAMPAIGN - EDIT -->
    <ApolloQuery
        v-if="isEditingDrip && currentEditor === 'edit:drip'"
        :query="(gql) => queries.dripCampaign.edit"
        :variables="editParam"
    >
        <template v-slot="{ result: { data, loading, error }, isLoading }">
            <template v-if="isLoading">
                <Modal>
                    <div
                        class="flex flex-col h-full w-full justify-center items-center bg-black bg-opacity-80"
                    >
                        <div
                            class="shadow border border-secondary rounded-lg p-6 bg-neutral"
                        >
                            <Spinner />
                        </div>
                    </div>
                </Modal>
            </template>

            <CampaignBuilder
                v-if="!isLoading && !!data"
                type="drip"
                @close="resetEditors"
                :campaign="data['dripCampaign']"
            />
        </template>
    </ApolloQuery>

    <!-- DRIP CAMPAIGN - CREATE -->
    <CampaignBuilder
        v-if="isEditingDrip && currentEditor === 'create:drip'"
        type="drip"
        @close="resetEditors"
    />

    <!-- SCHEDULED CAMPAIGN - EDIT -->
    <ApolloQuery
        v-if="isEditingScheduled && currentEditor === 'edit:scheduled'"
        :query="(gql) => queries.scheduledCampaign.edit"
        :variables="editParam"
    >
        <template v-slot="{ result: { data, loading, error }, isLoading }">
            <template v-if="isLoading">
                <Modal>
                    <div
                        class="flex flex-col h-full w-full justify-center items-center bg-black bg-opacity-80"
                    >
                        <div
                            class="shadow border border-secondary rounded-lg p-6 bg-neutral"
                        >
                            <Spinner />
                        </div>
                    </div>
                </Modal>
            </template>

            <CampaignBuilder
                v-if="!isLoading && !!data"
                type="scheduled"
                @close="resetEditors"
                :campaign="data['scheduledCampaign']"
            />
        </template>
    </ApolloQuery>

    <!-- SCHEDULED CAMPAIGN - CREATE -->
    <CampaignBuilder
        v-if="isEditingScheduled && currentEditor === 'create:scheduled'"
        type="scheduled"
        @close="resetEditors"
    />
</template>

<script setup>
import { ref } from "vue";
import queries from "@/gql/queries";

import ToolBar from "./components/ToolBar.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import CampaignBuilder from "./components/Creator/CampaignBuilder.vue";
import CampaignListDisplay from "./Partials/CampaignListDisplay.vue";
import Modal from "@/Components/ModalUnopinionated.vue";
import Spinner from "@/Components/Spinner.vue";

const props = defineProps({
    type: {
        type: String,
        default: "scheduled",
    },
});

const editParam = ref(null);

/**
 * indicates which type of campaign is being edited and if it's new or not
 *      edit:drip - create:drip
 *      edit:scheduled - create:scheduled
 */
const currentEditor = ref(null);

/**
 * Campaign type display & handling
 */
// const selectedTab = ref(props.type); // @TODO figure out how to tie the URL to this
const selectedTab = ref("scheduled");

const handleClickTabSchedule = () => {
    if (selectedTab.value === "scheduled") return;
    selectedTab.value = "scheduled";
};

const handleClickTabDrip = () => {
    if (selectedTab.value === "drip") return;
    selectedTab.value = "drip";
};

/**
 * Campaign creation/updating handling
 */
const isEditingDrip = ref(false);
const isEditingScheduled = ref(false);

const toggleDripBuilder = () => (isEditingDrip.value = !isEditingDrip.value);
const toggleScheduleBuilder = () =>
    (isEditingScheduled.value = !isEditingScheduled.value);

/**
 * Clear editor and management state for next event
 */
const resetEditors = () => {
    currentEditor.value = null;
    isEditingDrip.value = false;
    isEditingScheduled.value = false;
    editParam.value = null;
};

/**
 * Setup state for new campaign creation with the specified type
 */
const handleNewCampaign = (type = "scheduled") => {
    resetEditors();
    currentEditor.value = `create:${type}`;
    if (type === "scheduled") toggleScheduleBuilder();
    else if (type === "drip") toggleDripBuilder();
};

/**
 * Setup state to edit an existing campaign and prepare gql to fetch the full details of the campaign
 * for modifications
 */
const handleEditCampaign = (type, id) => {
    console.log("edit campaign called", type, id);
    if (!type || !["drip", "scheduled"].includes(type))
        throw new Error("unknown campaign type");

    if (!id || typeof id !== "string")
        throw new Error("cannot edit campaign without an id");
    currentEditor.value = `edit:${type}`;
    editParam.value = {
        id: id,
    };

    if (type === "drip") toggleDripBuilder();
    else if (type === "scheduled") toggleScheduleBuilder();
};
</script>

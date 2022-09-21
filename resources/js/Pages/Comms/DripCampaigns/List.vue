<template>
    <LayoutHeader title="Drip Campaigns">
        <div class="text-center">
            <h2 class="font-semibold text-xl leading-tight">
                Drip Campaigns Management
            </h2>
        </div>
        <div class="top-drop-row stop-drop-roll">
            <inertia-link
                class="btn justify-self-end"
                :href="route('mass-comms.dashboard')"
            >
                <span
                    ><font-awesome-icon
                        :icon="['far', 'chevron-double-left']"
                        size="sm"
                    />
                    Back</span
                >
            </inertia-link>
        </div>
    </LayoutHeader>
    <gym-revenue-crud
        base-route="mass-comms.drip-campaigns"
        model-name="Drip Campaign"
        model-key="campaign"
        :fields="fields"
        :resource="dripCampaigns"
        :actions="actions"
        :top-actions="{ create: { label: 'New Campaign' } }"
    />
    <confirm
        title="Really Trash?"
        v-if="confirmTrash"
        @confirm="handleConfirmTrash"
        @cancel="confirmTrash = null"
    >
        Are you sure you want to remove this campaign? It will unassign all
        audiences and/or templates.
    </confirm>
</template>
<style scoped>
.top-drop-row {
    @apply flex flex-row justify-center lg:justify-start md:ml-4;
}
</style>

<script>
import { computed, defineComponent, ref } from "vue";
import { comingSoon } from "@/utils/comingSoon.js";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";
import Confirm from "@/Components/Confirm.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faChevronDoubleLeft,
    faEllipsisH,
} from "@fortawesome/pro-regular-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Inertia } from "@inertiajs/inertia";
import { parseDate } from "@/utils";

library.add(faChevronDoubleLeft, faEllipsisH);

export default defineComponent({
    components: {
        LayoutHeader,
        FontAwesomeIcon,
        Confirm,
        GymRevenueCrud,
    },
    props: ["filters", "dripCampaigns"],
    setup(props) {
        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };
        const handleConfirmTrash = () => {
            Inertia.delete(
                route("mass-comms.drip-campaigns.trash", confirmTrash.value)
            );
            confirmTrash.value = null;
        };

        const fields = computed(() => {
            return [
                "name",
                {
                    name: "active",
                    label: "status",
                    props: {
                        getProps: ({ data }) =>
                            !!data.active
                                ? { text: "Active", class: "badge-success" }
                                : { text: "Draft", class: "badge-warning" },
                    },
                    export: (active) => (active ? "Active" : "Draft"),
                },
                { name: "type", transform: () => "Regular" },
                { name: "updated_at", label: "date updated" },
                {
                    name: "creator.name",
                    label: "updated by",
                    transform: (creator) => creator || "Auto Generated",
                },
            ];
        });

        const actions = computed(() => {
            return {
                edit: {
                    shouldRender: ({ data }) => {
                        if (!data?.active) {
                            return true;
                        }
                        if (!data?.schedule_date?.value) {
                            return true;
                        }
                        if (!parseDate(data?.schedule_date?.value)) {
                            console.log({ date: data?.schedule_date?.value });
                            return true;
                        }
                        return (
                            new Date(`${data?.schedule_date?.value} UTC`) >=
                            new Date()
                        );
                    },
                },
                results: {
                    label: "Results",
                    shouldRender: ({ data }) =>
                        data?.active &&
                        data?.schedule_date &&
                        new Date(`${data.schedule_date.value} UTC`) <
                            new Date(),
                    handler: () => comingSoon(),
                },
                trash: {
                    handler: ({ data }) => handleClickTrash(data.id),
                },
            };
        });
        return {
            handleClickTrash,
            confirmTrash,
            handleConfirmTrash,
            fields,
            actions,
        };
    },
});
</script>

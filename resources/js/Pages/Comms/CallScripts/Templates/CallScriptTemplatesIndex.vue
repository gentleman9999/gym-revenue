<template>
    <LayoutHeader title="Call Script Templates">
        <div class="text-center">
            <h2 class="font-semibold text-xl leading-tight">
                Call Script Template Management
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
        base-route="mass-comms.call-templates"
        model-name="Call Script Template"
        model-key="template"
        :fields="fields"
        :resource="templates"
        :actions="actions"
        :top-actions="{ create: { label: 'New Template' } }"
    />
    <confirm
        title="Really Trash?"
        v-if="confirmTrash"
        @confirm="handleConfirmTrash"
        @cancel="confirmTrash = null"
    >
        Are you sure you want to remove this template? It will be removed from
        any assigned campaigns.
    </confirm>
</template>
<style scoped>
.top-drop-row {
    @apply flex flex-row justify-center lg:justify-start md:ml-4;
}
</style>
<script>
import { computed, defineComponent, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Confirm from "@/Components/Confirm.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";

import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faChevronDoubleLeft,
    faEllipsisH,
} from "@fortawesome/pro-regular-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

library.add(faChevronDoubleLeft, faEllipsisH);

export default defineComponent({
    name: "CallScriptTemplatesIndex",
    components: {
        LayoutHeader,
        FontAwesomeIcon,
        Confirm,
        GymRevenueCrud,
    },
    props: ["title", "filters", "templates"],
    setup(props) {
        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };
        const handleConfirmTrash = () => {
            Inertia.delete(
                route("mass-comms.call-templates.trash", confirmTrash.value)
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
                        truthy: "Active",
                        falsy: "Draft",
                        getProps: ({ data }) =>
                            data.active
                                ? { text: "Active", class: "badge-success" }
                                : { text: "Draft", class: "badge-warning" },
                    },
                    export: (active) => (active ? "Active" : "Draft"),
                },
                {
                    name: "creator.name",
                    label: "updated by",
                    transform: (creator) => creator || "Auto Generated",
                },
                { name: "team.name", label: "team" },
                { name: "updated_at", label: "date updated" },
            ];
        });

        const actions = computed(() => {
            return {
                duplicate: {
                    label: "Duplicate Template",
                    handler: ({ data }) => {
                        Inertia.visitInModal(
                            route(
                                "mass-comms.call-templates.duplicate",
                                data.id
                            )
                        );
                    },
                },
                trash: {
                    handler: ({ data }) => handleClickTrash(data.id),
                },
            };
        });

        return {
            fields,
            actions,
            handleClickTrash,
            confirmTrash,
            handleConfirmTrash,
        };
    },
});
</script>

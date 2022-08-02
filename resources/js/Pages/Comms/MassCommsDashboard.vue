<template>
    <LayoutHeader title="Mass Communications Dashboard">
        <div class="text-center">
            <h2 class="font-semibold text-xl leading-tight">
                Mass Communication Dashboard
            </h2>
            <small>Send SMS and Emails in Bulk here!</small>
        </div>
    </LayoutHeader>

    <jet-bar-container class="space-y-8">
        <div class="shadow stats">
            <!--                <div class="stat">-->
            <!--                    <div class="stat-title">Audience</div>-->
            <!--                    <div class="stat-value">-->
            <!--                        {{ stats["total_audience"] }}-->
            <!--                    </div>-->
            <!--                    <div class="stat-desc">Total</div>-->
            <!--                </div>-->
            <mass-comm-stat
                v-for="(lbl, slug) in audiences"
                :title="lbl"
                :value="stats['audience_breakdown'][slug]"
            />
        </div>

        <div
            class="grid grid-cols-2 lg:flex flex-wrap xl:flex-row justify-center xl:justify-start gap-2 lg:gap-1"
        >
            <mass-comm-data-button
                :href="route('comms.email-templates')"
                :active="stats['email_templates'].active"
                :total="stats['email_templates'].created"
            >
                Email Templates
            </mass-comm-data-button>
            <mass-comm-data-button
                :href="route('comms.sms-templates')"
                :active="stats['sms_templates'].active"
                :total="stats['sms_templates'].created"
            >
                SMS Templates
            </mass-comm-data-button>
            <mass-comm-data-button
                :href="route('comms.scheduled-campaigns')"
                :active="stats['scheduled_campaigns'].active"
                :total="stats['scheduled_campaigns'].created"
            >
                Scheduled Campaigns
            </mass-comm-data-button>
            <mass-comm-data-button
                :href="route('comms.drip-campaigns')"
                :active="stats['drip_campaigns'].active"
                :total="stats['drip_campaigns'].created"
            >
                Drip Campaigns
            </mass-comm-data-button>
        </div>

        <!-- Page Content -->
        <gym-revenue-crud
            :resource="historyFeed"
            :fields="fields"
            model-key="comms-feed"
            :top-actions="topActions"
            :actions="false"
            :base-route="baseRoute"
            title-field="type"
            :on-double-click="false"
        >
            <template #filter>
                <simple-search-filter
                    v-model:modelValue="form.search"
                    class="w-full max-w-md mr-4"
                    @reset="reset"
                    @clear-filters="clearFilters"
                    @clear-search="clearSearch"
                >
                    <template #trigger>
                        <span class="inline-flex rounded-md">
                            <button
                                type="button"
                                class="btn btn-sm text-xs inline-flex items-center px-3 py-2 border border-white text-sm leading-4 font-medium rounded-md bg-white hover:bg-base-100 bg-base-200 focus:outline-none focus:bg-base-100 active:bg-base-100 transition"
                            >
                                {{
                                    form.audience in audiences
                                        ? "Audience: " +
                                          audiences[form.audience]
                                        : "Audiences"
                                }}

                                <svg
                                    class="ml-2 -mr-0.5 h-4 w-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </span>
                    </template>

                    <template #content>
                        <!-- Team Management -->
                        <template v-if="true">
                            <!-- Location Switcher -->
                            <div class="block px-4 py-2 text-xs">
                                Select an Audience(s)
                            </div>

                            <ul class="menu compact">
                                <li
                                    v-for="(lbl, slug) in audiences"
                                    :key="slug"
                                >
                                    <a
                                        href="#"
                                        @click.prevent="form.audience = slug"
                                    >
                                        <svg
                                            v-if="form.audience === slug"
                                            class="mr-2 h-5 w-5 text-green-400"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            ></path>
                                        </svg>
                                        {{ lbl }}
                                    </a>
                                </li>
                            </ul>
                        </template>
                    </template>
                </simple-search-filter>
            </template>
        </gym-revenue-crud>
    </jet-bar-container>
</template>

<script>
import { defineComponent } from "vue";
import { comingSoon } from "@/utils/comingSoon.js";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetDropdown from "@/Components/Dropdown.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";
import { Inertia } from "@inertiajs/inertia";
import SimpleSearchFilter from "@/Components/CRUD/SimpleSearchFilter.vue";
import { useSearchFilter } from "@/Components/CRUD/helpers/useSearchFilter";
import MassCommDataButton from "./Partials/MassCommDataButton.vue";
import MassCommStat from "./Partials/MassCommStat.vue";

export default defineComponent({
    name: "MassCommsDashboard",
    components: {
        LayoutHeader,
        JetDropdown,
        GymRevenueCrud,
        JetBarContainer,
        SimpleSearchFilter,
        MassCommDataButton,
        MassCommStat,
    },
    props: ["title", "audiences", "activeAudience", "stats", "historyFeed"],
    setup(props) {
        const baseRoute = "comms.dashboard";
        const { form, reset, clearFilters, clearSearch } = useSearchFilter(
            baseRoute,
            {
                audience: props.activeAudience,
            }
        );
        const fields = [
            { name: "operation", label: "action", sortable: false },
            { name: "entity", label: "Entity", sortable: false },
            { name: "entityObject.name", label: "Name", sortable: false },
            { name: "created_at", label: "date", sortable: false },
            { name: "user.name", label: "by", sortable: false },
        ];

        const topActions = {
            create: false,
            email: {
                label: "+ New Email",
                handler: () => comingSoon(),
                class: "btn-primary",
            },
            sms: {
                label: "+ New Sms",
                handler: () => comingSoon(),
                class: "btn-primary",
            },
            test: {
                label: "+ New Test SMS",
                handler: () => sendATestSMS(),
                class: "btn-primary",
            },
        };

        const sendATestSMS = (data) => {
            Inertia.post(route("comms.sms-templates.test-msg"));
        };
        const viewAudienceDashboard = (slug) => {
            Inertia.visit(route("comms.dashboard") + "?audience=" + slug);
        };
        return {
            fields,
            sendATestSMS,
            comingSoon,
            viewAudienceDashboard,
            topActions,
            baseRoute,
            form,
            reset,
            clearFilters,
            clearSearch,
        };
    },
});
</script>

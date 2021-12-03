<template>
    <app-layout :title="title">
        <template #header>
            <div class="text-center">
                <h2 class="font-semibold text-xl leading-tight">
                    Email Template Management
                </h2>
                <small></small>
            </div>
        </template>
        <jet-bar-container>
            <div class="flex flex-col pb-2">
                <div
                    class="top-drop-row stop-drop-roll flex flex-row justify-center mb-4 xl-justify-left"
                >
                    <inertia-link
                        class="btn justify-self-end"
                        :href="route('comms.dashboard')"
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
            </div>
            <div
                class="top-navigation flex flex-col xl:flex-row xl-justify-between"
            >
                <div
                    class="flex flex-wrap xl:flex-row justify-center xl-justify-left"
                >
                    <div class="mr-1">
                        <search-filter
                            v-model:modelValue="form.search"
                            class="w-full max-w-md mr-4"
                            @reset="reset"
                        >
                            <div class="block py-2 text-xs">Trashed:</div>
                            <select
                                v-model="form.trashed"
                                class="mt-1 w-full form-select"
                            >
                                <option :value="null" />
                                <option value="with">With Trashed</option>
                                <option value="only">Only Trashed</option>
                            </select>
                        </search-filter>
                    </div>
                </div>

                <div class="flex flex-row justify-center xl-justify-right">
                    <div class="mt-2 ml-1 xl:mt-0">
                        <inertia-link
                            class="btn justify-self-end"
                            :href="route('comms.email-templates.create')"
                        >
                            <span>+ New Template</span>
                        </inertia-link>
                    </div>
                </div>
            </div>
            <div class="inner-template-index-content mt-4 pb-10">
                <div
                    class="template-table border-2 border-base-300 rounded-t-md"
                >
                    <!--                    <div class="flex flex-col bg-secondary rounded-t-md">-->
                    <!--                        <div class="border-b-2 border-gray-300 py-4">-->
                    <!--                            <h2 class="px-4">Templates</h2>-->
                    <!--                        </div>-->
                    <!--                    </div>-->
                    <gym-revenue-table :headers="tableHeaders">
                        <!--                        <template #prethead>-->
                        <!--                            <div class="p-4 bg-base-100"></div>-->
                        <!--                            <div class="p-4 bg-base-100"></div>-->
                        <!--                        </template>-->
                        <tr v-if="templates.data.length === 0">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>No Data Available.</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr
                            class="hover"
                            v-else
                            v-for="(template, idx) in templates.data"
                            :key="idx"
                        >
                            <td>{{ template.name }}</td>
                            <td>
                                <div
                                    class="badge"
                                    :class="badgeClasses(template.active)"
                                >
                                    {{ template.active ? "Live" : "Draft" }}
                                </div>
                            </td>
                            <td>Regular</td>
                            <td>{{ template.updated_at }}</td>
                            <td>
                                {{
                                    template.creator !== null
                                        ? template.creator.name
                                        : "Auto Generated"
                                }}
                            </td>
                            <td>
                                <div class="ml-3 relative">
                                    <jet-dropdown align="end" width="40">
                                        <template #trigger>
                                            <span
                                                class="inline-flex rounded-md"
                                            >
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-white text-sm leading-4 font-medium rounded-md bg-white hover:bg-base-100 bg-base-200 focus:outline-none focus:bg-base-100 active:bg-base-100 transition"
                                                >
                                                    <font-awesome-icon
                                                        :icon="[
                                                            'far',
                                                            'ellipsis-h',
                                                        ]"
                                                        size="lg"
                                                    />
                                                </button>
                                            </span>
                                        </template>
                                        <template #content>
                                            <div class="w-60">
                                                <div
                                                    class="block px-4 py-2 text-xs"
                                                >
                                                    Available Actions
                                                    <br />
                                                </div>
                                                <ul class="menu compact">
                                                    <li
                                                        v-for="(
                                                            option, slug
                                                        ) in actionOptions(
                                                            template
                                                        )"
                                                        :key="slug"
                                                    >
                                                        <a
                                                            @click.prevent.stop="
                                                                option.click
                                                            "
                                                            :href="option.url"
                                                        >
                                                            {{ option.label }}
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </template>
                                    </jet-dropdown>
                                </div>
                            </td>
                        </tr>
                    </gym-revenue-table>
                </div>
            </div>
        </jet-bar-container>
        <confirm
            title="Really Trash?"
            v-if="confirmTrash"
            @confirm="handleConfirmTrash"
        >
            Are you sure you want to trash this template?  It will be removed from any assigned campaigns.
        </confirm>
    </app-layout>
</template>

<script>
import { defineComponent, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import JetDropdown from "@/Components/Dropdown";
import JetBarContainer from "@/Components/JetBarContainer";
import SearchFilter from "@/Components/SearchFilter";
import GymRevenueTable from "@/Components/GymRevenueTable";
import Confirm from "@/Components/Confirm";

import { library } from "@fortawesome/fontawesome-svg-core";
import {
    faChevronDoubleLeft,
    faEllipsisH,
} from "@fortawesome/pro-regular-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import mapValues from "lodash/mapValues";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import { Inertia } from "@inertiajs/inertia";

library.add(faChevronDoubleLeft, faEllipsisH);

export default defineComponent({
    name: "EmailTemplatesIndex",
    components: {
        AppLayout,
        JetDropdown,
        SearchFilter,
        FontAwesomeIcon,
        GymRevenueTable,
        JetBarContainer,
        Confirm,
    },
    props: ["title", "filters", "templates"],
    setup(props) {
        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };
        const handleConfirmTrash = () => {
            Inertia.delete(route("comms.email-templates.trash", confirmTrash.value));
            confirmTrash.value = null;
        };
        return { handleClickTrash, confirmTrash, handleConfirmTrash };
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function () {
                this.$inertia.get(
                    this.route("comms.email-templates"),
                    pickBy(this.form),
                    {
                        preserveState: true,
                        preserveScroll: true,
                    }
                );
            }, 150),
        },
    },
    data() {
        return {
            form: {
                search: this.filters.search,
                trashed: this.filters.trashed,
            },
        };
    },
    computed: {
        tableHeaders() {
            if (this.templates.data.length > 0) {
                return [
                    "name",
                    "status",
                    "type",
                    "date updated",
                    "updated by",
                    "",
                ];
            }

            return [];
        },
    },
    methods: {
        comingSoon() {
            new Noty({
                type: "warning",
                theme: "sunset",
                text: "Feature Coming Soon!",
                timeout: 7500,
            }).show();
        },
        actionOptions(template) {
            return {
                edit: {
                    url: "#",
                    label: "Edit",
                    click: () =>
                        Inertia.visit(
                            route("comms.email-templates.edit", template.id)
                        ),
                },
                selfSend: {
                    url: "#",
                    label: "Send You a Test Email",
                    click: () => this.comingSoon(),
                },
                delete: {
                    url: "#",
                    label: "Trash",
                    click: () => this.handleClickTrash(template.id),
                },
            };
        },
        reset() {
            this.form = mapValues(this.form, () => null);
        },
        badgeClasses(status) {
            return {
                "badge-success": status,
                "badge-warning": !status,
            };
        },
    },
    mounted() {},
});
</script>

<style scoped>
@media (min-width: 1024px) {
    .lg-w-full {
        width: 100%;
    }

    .lg-w-30 {
        width: 30%;
    }

    .lg-w-70 {
        width: 70%;
    }
}

@media (min-width: 1280px) {
    .xl-justify-left {
        justify-content: flex-start;
    }

    .xl-justify-right {
        justify-content: flex-end;
    }

    .xl-justify-between {
        justify-content: space-between;
    }
}
</style>

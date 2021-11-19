<template>
    <app-layout :title="title">
        <template #header>
            <div class="text-center">
                <h2 class="font-semibold text-xl  leading-tight">
                    SMS Campaigns Management
                </h2>
                <small></small>
            </div>
            <jet-bar-container>
                <div class="flex flex-col pb-2">
                    <div class="top-drop-row stop-drop-roll flex flex-row justify-center mb-4 xl-justify-left">
                        <Link
                            class="btn justify-self-end"
                            :href="route('comms.dashboard')">
                            <span><font-awesome-icon :icon="['far', 'chevron-double-left']" size="16"/> Back</span>
                        </Link>
                    </div>
                </div>
                <div class="top-navigation flex flex-col xl:flex-row xl-justify-between">
                    <div class="flex flex-wrap xl:flex-row justify-center xl-justify-left">
                        <div class="mr-1">
                            <search-filter v-model:modelValue="form.search" class="w-full max-w-md mr-4" @reset="reset">
                                <div class="block py-2 text-xs ">Trashed:</div>
                                <select v-model="form.trashed" class="mt-1 w-full form-select">
                                    <option :value="null"/>
                                    <option value="with">With Trashed</option>
                                    <option value="only">Only Trashed</option>
                                </select>
                            </search-filter>
                        </div>
                    </div>

                    <div class="flex flex-row justify-center xl-justify-right">
                        <div class="mt-2 ml-1 xl:mt-0">
                            <Link
                                class="btn justify-self-end"
                                href="#" @click="comingSoon()">
                                <span>Quick Send</span>
                            </Link>
                        </div>

                        <div class="mt-2 ml-1 xl:mt-0">
                            <Link
                                class="btn justify-self-end"
                                href="#" @click="comingSoon()">
                                <span>+ New Campaign</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </jet-bar-container>
        </template>
    </app-layout>
</template>

<script>
import {defineComponent} from "vue";
import {Link} from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue'
import JetDropdown from '@/Jetstream/Dropdown'
import JetDropdownLink from '@/Jetstream/DropdownLink'
import JetBarContainer from "@/Components/JetBarContainer";
import SearchFilter from "@/Components/SearchFilter";

import { library } from '@fortawesome/fontawesome-svg-core';
import { faChevronDoubleLeft } from '@fortawesome/pro-regular-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import mapValues from "lodash/mapValues";
library.add(faChevronDoubleLeft)

export default defineComponent({
    name: "SMSCampaignsIndex",
    components: {
        Link,
        AppLayout,
        JetDropdown,
        SearchFilter,
        FontAwesomeIcon,
        JetBarContainer,
        JetDropdownLink
    },
    props: ['title', 'filters'],
    setup(props) {},
    watch: {},
    data() {
        return {
            form: {
                search: this.filters.search,
                trashed: this.filters.trashed,
            },
        };
    },
    computed: {

    },
    methods: {
        comingSoon() {
            new Noty({
                type: 'warning',
                theme: 'sunset',
                text: 'Feature Coming Soon!',
                timeout: 7500
            }).show();
        },
        reset() {
            this.form = mapValues(this.form, () => null)
        },
    },
    mounted() {}
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

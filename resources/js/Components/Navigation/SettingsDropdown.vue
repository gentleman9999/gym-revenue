<template>
    <jet-dropdown align="end" width="48">
        <template #trigger>
            <button
                v-if="$page.props.jetstream.managesProfilePhotos"
                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-base-100-300 transition"
            >
                <img
                    class="h-8 w-8 rounded-full object-cover"
                    :src="$page.props.user.profile_photo_url"
                    :alt="$page.props.user.name"
                />
            </button>

            <span v-else class="inline-flex rounded-md">
                <button
                    type="button"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md focus:outline-none transition"
                >
                    {{ $page.props.user.name }}

                    <svg
                        class="ml-2 -mr-0.5 h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
            </span>
        </template>

        <template #content>
            <!-- Account Management -->
            <div class="block px-4 py-2 text-xs">Manage Account</div>
            <ul class="menu compact">
                <li>
                    <inertia-link :href="route('profile.show')">
                        Profile
                    </inertia-link>
                </li>
                <li v-if="showClientSettings">
                    <inertia-link :href="route('settings')">
                        Settings
                    </inertia-link>
                </li>
                <li>
                    <!-- @todo - make these dynamic, as some users wont have access -->
                    <inertia-link :href="route('users')">
                        User Management
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('profile.show')">
                        Invoices
                    </inertia-link>
                </li>

                <li
                    v-if="
                        !('is_being_impersonated' in $page.props.user) &&
                        ($page.props.user.abilities.includes(
                            'users.impersonate'
                        ) ||
                            $page.props.user.abilities.includes('*'))
                    "
                >
                    <a
                        @click.prevent="
                            openImpersonation($page.props.user.abilities)
                        "
                    >
                        Impersonation
                    </a>
                </li>
                <li>
                    <inertia-link
                        :href="route('api-tokens.index')"
                        v-if="$page.props.jetstream.hasApiFeatures"
                    >
                        API Tokens
                    </inertia-link>
                </li>
            </ul>

            <div class="border-t border-base-100-100"></div>
            <!-- Extra Features -->
            <!-- @todo - make these dynamic, as some users wont have access -->
            <div class="block px-4 py-2 text-xs">Extras</div>
            <ul class="menu compact">
                <li>
                    <inertia-link :href="route('workout-generator')">
                        Workout Generator
                    </inertia-link>
                </li>
                <li>
                    <inertia-link :href="route('workout-generator')">
                        Sales Slideshow
                    </inertia-link>
                </li>
            </ul>

            <div class="border-t border-base-100-100"></div>
            <ul class="menu compact">
                <li>
                    <inertia-link
                        href="#"
                        @click="logout"
                        v-if="!('is_being_impersonated' in $page.props.user)"
                    >
                        Log Out</inertia-link
                    >
                    <inertia-link
                        href="#"
                        @click="leaveImpersonationMode"
                        v-else
                    >
                        Leave Impersonation Mode</inertia-link
                    >
                </li>
            </ul>
        </template>
    </jet-dropdown>
    <daisy-modal
        title="Impersonation Mode"
        width="45%"
        overlayTheme="dark"
        modal-theme="dark"
        ref="impModal"
        @close="impVars.showModal = false"
    >
        <list-of-users-to-impersonate
            v-if="impVars.showModal"
            @close="impVars.closeModal()"
        ></list-of-users-to-impersonate>
    </daisy-modal>
</template>

<script>
import { defineComponent, computed, ref } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import JetDropdown from "@/Components/Dropdown";
import DaisyModal from "@/Components/DaisyModal";
import ListOfUsersToImpersonate from "@/Presenters/Impersonation/ListOfUserstoImpersonate";
import { Inertia } from "@inertiajs/inertia";

export default defineComponent({
    components: {
        JetDropdown,
        DaisyModal,
        ListOfUsersToImpersonate,
    },
    props: {
        logout: {
            type: Function,
        },
    },
    setup() {
        const page = usePage();
        let impVars = {
            showModal: false,
            closeModal() {
                impModal.value.close();
            },
        };
        const impModal = ref(null);
        const openImpersonation = (abilities) => {
            if (
                abilities.includes("users.impersonate") ||
                abilities.includes("*")
            ) {
                impVars.showModal = true;
                impModal.value.open();
            } else {
                new Noty({
                    type: "warning",
                    theme: "sunset",
                    text: "You Cant Do That!",
                    timeout: 7500,
                }).show();
            }
        };

        const leaveImpersonationMode = () => {
            Inertia.post(route("impersonation.stop", {}));
        };
        const showClientSettings = computed(
            () => page.props.value.user.current_client_id
        );
        return {
            showClientSettings,
            openImpersonation,
            leaveImpersonationMode,
            impModal,
            impVars,
        };
    },
});
</script>

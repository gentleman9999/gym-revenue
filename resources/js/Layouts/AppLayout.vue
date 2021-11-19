<template>
    <div>
        <Head :title="title" />
        <jet-banner />
        <div class="font-sans antialiased min-h-screen">
            <top-nav/>
            <div class="font-sans antialiased">
                <jet-banner />

                <div class="lg:flex flex-col lg:flex-row lg:min-h-screen w-full">

                    <!-- Sidebar -->
                    <side-nav @toggle="showingSidebar = !showingSidebar"/>
                    <!-- End Sidebar -->

                    <div class="w-full">
                        <!-- Content Container -->
                        <main class="flex-1 relative z-0 overflow-y-auto py-6 focus:outline-none" tabindex="0">
                            <div v-if="this.$slots.header" class="max-w-7xl mx-auto pt-3 px-4 sm:px-6 lg:px-8">
                                <!-- Title -->
                                <h1 class="text-lg font-semibold tracking-widest  uppercase dark-mode:text-white">
                                    <slot name="header"></slot>
                                </h1>
                                <!-- End Title -->
                            </div>
                            <!-- Replace with your content -->
                            <div>
                                <!-- Content -->
                                <div class="min-h-full lg:min-h-96 px-4 sm:px-0">
                                    <slot></slot>
                                </div>
                                <!-- End Content -->
                            </div>
                            <!-- /End replace -->
                        </main>
                        <!-- Content Container -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'
    import JetApplicationMark from '@/Jetstream/ApplicationMark'
    import JetBanner from '@/Jetstream/Banner'
    import JetNavLink from '@/Jetstream/NavLink'
    import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
    import JetBarNavigationMenu from "@/Components/JetBarNavigationMenu";
    import TopNav from "@/Components/Navigation/TopNav";
    import SideNav from "@/Components/Navigation/SideNav";
    import { Head, Link } from '@inertiajs/inertia-vue3';
    import NotyBell from "@/Components/NotyBell";

    export default defineComponent({
        components: {
            Head,
            Link,
            JetBarNavigationMenu,
            JetApplicationMark,
            JetBanner,
            JetNavLink,
            JetResponsiveNavLink,
            NotyBell,
            TopNav,
            SideNav
        },
        props: {
            title: String,
        },
        data() {
            return {
                showingSidebar: true,
                showingNavigationDropdown: false,
                showingNotificationDropdown: false,
            }
        },
        methods: {
            switchToTeam(team) {
                this.$inertia.put(route('current-team.update'), {
                    'team_id': team.id
                }, {
                    preserveState: false
                })
            },
            switchToLocation(location) {
                this.$inertia.put(route('current-location.update'), {
                    'location_id': location.id
                }, {
                    preserveState: false,
                    headers:{
                        Accept: 'application/json',
                        Test:'123'
                    }
                })
            },

            comingSoon() {
                new Noty({
                    type: 'warning',
                    theme: 'sunset',
                    text: 'Feature Coming Soon!',
                    timeout: 7500
                }).show();
            },
            logout() {
                this.$inertia.post(route('logout'));
            },
        }
    })
</script>

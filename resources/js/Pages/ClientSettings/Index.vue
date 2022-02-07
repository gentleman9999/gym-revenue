<template>
    <app-layout title="Settings">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Settings</h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div>
                    <client-services-form
                        :user="$page.props.user"
                        :services="services"
                        :available-services="availableServices"
                    />

                    <jet-section-border />
                </div>

                <div>
                    <trial-membership-form
                        :user="$page.props.user"
                        :trial-membership-types="trialMembershipTypes"
                        :locations="locations"
                        v-if="
                            services.filter(
                                (detail) => detail.value === 'free-trial'
                            ).length
                        "
                    />

                    <jet-section-border />
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import ClientServicesForm from "./Partials/ClientServicesForm";
import TrialMembershipForm from "./Partials/TrialMembershipForm";

export default defineComponent({
    components: {
        AppLayout,
        JetSectionBorder,
        ClientServicesForm,
        TrialMembershipForm,
    },
    props: {
        services: {
            type: Array,
        },
        availableServices: {
            type: Array,
        },
        trialMembershipTypes: {
            type: Array,
        },
        locations: {
            type: Array
        }
    },
});
</script>

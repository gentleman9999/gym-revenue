<template>
    <LayoutHeader title="Client Settings">
        <h2 class="font-semibold text-xl leading-tight">Client Settings</h2>
    </LayoutHeader>

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
                <comm-pref-form
                    :user="$page.props.user"
                    :comm-preferences="commPreferences"
                    :available-comm-preferences="availableCommPreferences"
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
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import ClientServicesForm from "./Partials/ClientServicesForm";
import CommPrefForm from "./Partials/CommPrefForm";
import TrialMembershipForm from "./Partials/TrialMembershipForm";

export default defineComponent({
    components: {
        LayoutHeader,
        JetSectionBorder,
        ClientServicesForm,
        TrialMembershipForm,
        CommPrefForm,
    },
    props: {
        services: {
            type: Array,
        },
        availableServices: {
            type: Array,
        },
        commPreferences: {
            type: Array,
        },
        availableCommPreferences: {
            type: Array,
        },
        trialMembershipTypes: {
            type: Array,
        },
        locations: {
            type: Array,
        },
    },
});
</script>

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
                <social-media-form
                    :user="$page.props.user"
                    :social-medias="socialMedias"
                    :logo-url="logoUrl"
                />

                <jet-section-border />
            </div>

            <file-manager />
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import ClientServicesForm from "./Partials/ClientServicesForm.vue";
import CommPrefForm from "./Partials/CommPrefForm.vue";
import SocialMediaForm from "./Partials/SocialMediaForm.vue";
import FileManager from "./Partials/FileManager.vue";

export default defineComponent({
    components: {
        LayoutHeader,
        JetSectionBorder,
        ClientServicesForm,
        CommPrefForm,
        SocialMediaForm,
        FileManager,
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
        socialMedias: {
            type: Array,
        },
        logoUrl: {
            type: String,
        },
    },
});
</script>

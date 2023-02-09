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
                    :gateways="gateways"
                />

                <jet-section-border />
            </div>

            <div>
                <entry-source-form
                    :user="$page.props.user"
                    :entry-sources="entrySources"
                    :available-entry-sources="availableEntrySources"
                />

                <jet-section-border />
            </div>

            <div>
                <entry-source-category-form
                    :user="$page.props.user"
                    :entry-source-categories="entrySourceCategories"
                    :available-entry-source-categories="
                        availableEntrySourceCategories
                    "
                />

                <jet-section-border />
            </div>

            <div>
                <social-media-form
                    :user="$page.props.user"
                    :social-medias="socialMedias"
                    :available-social-medias="availableSocialMedias"
                    :logo-url="logoUrl"
                />

                <jet-section-border />
            </div>
            <div>
                <gateway-form :user="$page.props.user" :gateways="gateways" />

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
import GatewayForm from "@/Pages/ClientSettings/Partials/GatewayForm.vue";
import EntrySourceForm from "./Partials/EntrySourceForm.vue";
import EntrySourceCategoryForm from "./Partials/EntrySourceCategoryForm.vue";

export default defineComponent({
    components: {
        GatewayForm,
        LayoutHeader,
        JetSectionBorder,
        ClientServicesForm,
        CommPrefForm,
        SocialMediaForm,
        FileManager,
        EntrySourceForm,
        EntrySourceCategoryForm,
    },
    props: {
        services: {
            type: Array,
            required: true,
        },
        availableServices: {
            type: Array,
            required: true,
        },
        commPreferences: {
            type: Array,
            required: true,
        },
        availableCommPreferences: {
            type: Object,
            required: true,
        },
        trialMembershipTypes: {
            type: Array,
            required: true,
        },
        locations: {
            type: Array,
            required: true,
        },
        socialMedias: {
            type: Array,
            required: true,
        },
        entrySources: {
            type: Array,
            required: true,
        },
        availableEntrySources: {
            type: Object,
            required: true,
        },
        entrySourceCategories: {
            type: Array,
            required: true,
        },
        availableEntrySourceCategories: {
            type: Object,
            required: true,
        },
        availableSocialMedias: {
            type: Array,
            required: true,
        },
        gateways: {
            type: Array,
            required: true,
        },
        logoUrl: {
            type: String,
            required: true,
        },
    },
});
</script>

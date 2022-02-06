<template>
    <app-layout title="Create New Lead">
        <template #header>
            <h2 class="font-semibold text-xl  leading-tight">
                Create New Lead
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <lead-form
                    :user-id="user_id"
                    :client-id="$page.props.user.current_client_id"
                    :locations="locations"
                    :lead_types="lead_types"
                    :lead_statuses="lead_statuses"
                    :lead_sources="lead_sources"
                    :lead_owners="lead_owners"
                    :available_services="available_services"/>
            </div>
        </div>
    </app-layout>
</template>

<script>
import {defineComponent} from 'vue'
import AppLayout from '@/Layouts/AppLayout'
import Button from '@/Components/Button'
import JetFormSection from '@/Jetstream/FormSection'

import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'

import LeadForm from '@/Pages/Leads/Partials/LeadForm'


export default defineComponent({
    components: {
        AppLayout,
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
        LeadForm
    },
    props: ['user_id',
        'locations',  'lead_types', 'lead_sources',
         'available_services', 'lead_owners',
        'lead_statuses'
    ],
    mounted() {
        console.log('lead_statuses ', this.$page.props.lead_statuses)
    }


})
</script>

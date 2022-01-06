<template>
    <app-layout title="Edit Lead">
        <template #header>
            <jet-bar-icon type="g0back" fill/>
            <h2 class="font-semibold text-xl  leading-tight">
                Edit Lead
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <lead-form :client-id="this.$page.props.user.current_client_id" :lead="lead" :locations="locations"
                           :lead_types="lead_types" :membership_types="membership_types"
                           :lead_sources="lead_sources" :available_services="available_services"/>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Button from '@/Components/Button'
import JetFormSection from '@/Jetstream/FormSection'

import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetBarIcon from '@/Components/JetBarIcon'

import LeadForm from '@/Pages/Leads/Partials/LeadForm'
import {defineComponent} from 'vue'


export default defineComponent({
    components: {
        AppLayout,
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
        JetBarIcon,
        LeadForm,
    },
    props: ['lead', 'locations', 'lead_types', 'lead_sources', 'membership_types', 'available_services']
})
</script>

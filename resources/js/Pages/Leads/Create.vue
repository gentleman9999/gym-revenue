<template>
    <app-layout title="Create New Lead">
        <template #header>
            <h2 class="font-semibold text-xl  leading-tight">
                Create New Lead
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <lead-form :client-id="$page.props.user.current_client_id" :locations="locations"/>
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

import LeadForm from '@/Pages/Leads/Partials/LeadCreateForm'


export default defineComponent({
    components: {
        AppLayout,
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
        LeadForm
    },
    props: ['locations'],
    mounted() {
        console.log('Fuck ', this.$page.props.user.current_client_id)
    }


})
</script>

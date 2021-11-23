<template>
    <app-layout title="Edit Location">
        <template #header>
            <jet-bar-icon type="g0back" fill/>
            <h2 class="font-semibold text-xl  leading-tight">
                Edit Location
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <location-form :client-id="this.$page.props.user.current_client_id" :location="$page.props.location"/>
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

import LocationForm from '@/Pages/Locations/Partials/LocationForm'
import {defineComponent} from 'vue'


export default defineComponent({
    components: {
        AppLayout,
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
        JetBarIcon,
        LocationForm,
    },
    props: ['locations']
})
</script>

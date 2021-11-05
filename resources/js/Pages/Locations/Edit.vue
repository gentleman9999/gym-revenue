<template>
    <app-layout title="Edit Location">
        <template #header>
            <jet-bar-icon type="g0back" fill/>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
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
import AppLayout from '@/Layouts/AppLayout.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetBarIcon from '@/Components/JetBarIcon.vue'

import LocationForm from '@/Pages/Locations/Partials/LocationForm.vue'


export default {
    components: {
        AppLayout,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetBarIcon,
        LocationForm,
    },
    props: ['locations']
}
</script>

<template>
    <app-layout title="Edit User">
        <template #header>
            <h2 class="font-semibold text-xl  leading-tight">
                Edit User {{ $page.props.selectedUser.name }}
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <user-form :client-id="$page.props.user['current_client_id']" :user="selectedUser" />
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

import UserForm from '@/Pages/Users/Partials/UserForm'


export default defineComponent({
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        UserForm
    },
    props: ['selectedUser']
})
</script>

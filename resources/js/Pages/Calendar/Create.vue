<template>
    <app-layout title="Create Event">
        <template #header>
            <div class="flex flex-row ml-4">
                <Button type="button" @click="$inertia.visit(route('calendar'))"  info outline>
                     Back
                </Button>

            </div>

        </template>

        <div>

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



export default defineComponent({
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },

})
</script>

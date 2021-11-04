<template>
    <jet-form-section @submitted="form.post('/locations')">
        <template #title>
            Location Details
        </template>

        <template #description>
            Create a new locations.
            <!--TODO: consider outputting client logo and name here-->
        </template>
        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Location Name"/>
                <jet-input id="name" type="text" class="block w-full mt-1" v-model="form.name" autofocus/>
                <jet-input id="client_id" type="hidden" v-model="form.client_id"/>

                <jet-input-error :message="form.errors.name" class="mt-2"/>
                <jet-input-error :message="form.errors.client_id" class="mt-2"/>
            </div>
        </template>

        <template #actions>
            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Create
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
import {useForm} from '@inertiajs/inertia-vue3'


import AppLayout from '@/Layouts/AppLayout.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'

export default {
    components: {
        AppLayout,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
    },
    props: ['clientId'],
    setup(props, context) {
        console.log({props, context})
        const form = useForm({
            name: null,
            client_id: props.clientId
        })

        return {form}
    },
}
</script>

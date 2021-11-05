<template>
    <jet-form-section @submitted="handleSubmit">
<!--        <template #title>-->
<!--            Location Details-->
<!--        </template>-->

<!--        <template #description>-->
<!--            {{ buttonText }} a location.-->
<!--        </template>-->
        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Name"/>
                <jet-input id="name" type="text" class="block w-full mt-1" v-model="form.name" autofocus/>
                <jet-input id="client_id" type="hidden" v-model="form.client_id"/>

                <jet-input-error :message="form.errors.name" class="mt-2"/>
                <jet-input-error :message="form.errors.client_id" class="mt-2"/>
            </div>
        </template>

        <template #actions>
            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ buttonText }}
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
    props: ['clientId', 'location'],
    setup(props, context) {
        let location = props.location;
        let operation = 'Update';
        if (!location) {
            location = {
                name: null,
                client_id: props.clientId
            }
            operation = 'Create';
        }

        const form = useForm(location)

        let handleSubmit = () => form.put(`/locations/${location.id}`);
        if(operation === 'Create'){
            handleSubmit = () => form.post('/locations');
        }


        return {form, buttonText: operation, handleSubmit}
    },
}
</script>

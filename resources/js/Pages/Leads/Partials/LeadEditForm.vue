<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-3">
                <jet-label for="first_name" value="First Name"/>
                <input id="" type="text" class="block w-full mt-1" v-model="form['first_name']" autofocus/>
                <jet-input-error :message="form.errors['first_name']" class="mt-2"/>
            </div>
            <div class="col-span-3">
                <jet-label for="last_name" value="Last Name"/>
                <input id="last_name" type="text" class="block w-full mt-1" v-model="form['last_name']" autofocus/>
                <jet-input-error :message="form.errors['last_name']" class="mt-2"/>
            </div>
            <div  class="col-span-3">
                <jet-label for="email" value="Email"/>
                <input id="email" type="text" class="block w-full mt-1" v-model="form.email" autofocus/>
                <jet-input-error :message="form.errors.email" class="mt-2"/>
            </div>
            <div  class="col-span-3">
                <jet-label for="mobile_phone" value="Phone"/>
                <input id="mobile_phone" type="text" class="block w-full mt-1" v-model="form['mobile_phone']" autofocus/>
                <jet-input-error :message="form.errors.phone" class="mt-2"/>
            </div>
        </template>

        <template #actions>
            <!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <Button type="button" @click="goBack" :class="{ 'opacity-25': form.processing }" error outline :disabled="form.processing">
                Cancel
            </Button>
            <div class="flex-grow" />
            <Button :class="{ 'opacity-25': form.processing }" class="btn-primary" :disabled="form.processing"  :loading="form.processing">
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import {useForm, usePage} from '@inertiajs/inertia-vue3'

import AppLayout from '@/Layouts/AppLayout'
import Button from '@/Components/Button'
import JetFormSection from '@/Jetstream/FormSection'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import {useGoBack} from '@/utils';

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
    },
    props: ['clientId', 'lead'],
    setup(props, context) {
        let lead = props.lead;
        let operation = 'Update';
        if (!lead) {
            lead = {
                'first_name': null,
                'last_name': null,
                email: null,
                phone: null,
                client_id: this.clientId
            }
            operation = 'Create';
        }

        const form = useForm(lead)

        let handleSubmit = () => form.put(`/data/leads/${lead.id}`);
        if (operation === 'Create') {
            handleSubmit = () => form.post('/data/leads');
        }

        const goBack = useGoBack(route('data.leads'));
        return {form, buttonText: operation, handleSubmit, goBack}
    },
}
</script>

<style scoped>

</style>

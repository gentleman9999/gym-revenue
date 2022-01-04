<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <font-awesome-icon icon="user-circle" size="6x" class="self-center opacity-10"/>
            </div>
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
            <div class="col-span-3">
                <jet-label for="email" value="Email"/>
                <input id="email" type="text" class="block w-full mt-1" v-model="form.email" autofocus/>
                <jet-input-error :message="form.errors.email" class="mt-2"/>
            </div>
            <div class="col-span-3">
                <jet-label for="mobile_phone" value="Mobile Phone"/>
                <input id="mobile_phone" type="text" class="block w-full mt-1" v-model="form['mobile_phone']"
                       autofocus/>
                <jet-input-error :message="form.errors.mobile_phone" class="mt-2"/>
            </div>
            <div class="col-span-3">
                <jet-label for="home_phone" value="Home Phone"/>
                <input id="home_phone" type="text" class="block w-full mt-1" v-model="form['home_phone']"
                       autofocus/>
                <jet-input-error :message="form.errors.home_phone" class="mt-2"/>
            </div>
            <div class="col-span-4">
                <jet-label for="club_id" value="Club"/>
                <select class="w-full border-base-100-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 rounded-md shadow-sm" v-model="form['gr_location_id']" required>
                    <option value="">Select a Club</option>
                    <option v-for="(name, clubId) in locations" :value="clubId">{{ name }}</option>
                </select>
                <jet-input-error :message="form.errors['gr_location_id']" class="mt-2"/>
            </div>
        </template>

        <template #actions>
            <!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <Button type="button" @click="goBack" :class="{ 'opacity-25': form.processing }" error outline
                    :disabled="form.processing">
                Cancel
            </Button>
            <div class="flex-grow"/>
            <Button :class="{ 'opacity-25': form.processing }" class="btn-primary" :disabled="form.processing"
                    :loading="form.processing">
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import {useForm} from '@inertiajs/inertia-vue3'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {library} from "@fortawesome/fontawesome-svg-core";
import {faUserCircle} from "@fortawesome/pro-solid-svg-icons";

import AppLayout from '@/Layouts/AppLayout'
import Button from '@/Components/Button'
import JetFormSection from '@/Jetstream/FormSection'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import {useGoBack} from '@/utils';

library.add(faUserCircle);

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,
        FontAwesomeIcon,
        JetInputError,
        JetLabel,
    },
    props: ['clientId', 'lead', 'locations'],
    setup(props, context) {
        let lead = props.lead;
        let operation = 'Update';
        if (!lead) {
            lead = {
                'first_name': null,
                'last_name': null,
                email: null,
                mobile_phone: null,//TODO:change to primary/alternate
                home_phone: null,
                client_id: props.clientId,
                gr_location_id: null
            }
            operation = 'Create';
        }

        const form = useForm(lead)

        let handleSubmit = () => form.put(`/data/leads/${lead.id}`);
        if (operation === 'Create') {
            handleSubmit = () => form.post('/data/leads/create');
        }

        const goBack = useGoBack(route('data.leads'));
        return {form, buttonText: operation, handleSubmit, goBack}
    },
}
</script>

<style scoped>

</style>

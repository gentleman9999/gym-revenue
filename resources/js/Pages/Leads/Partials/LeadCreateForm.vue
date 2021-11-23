<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-3">
                <jet-label for="first_name" value="First Name"/>
                <input id="" type="text" class="block w-full mt-1" v-model="form['first_name']" autofocus required/>
                <jet-input-error :message="form.errors['first_name']" class="mt-2"/>
            </div>
            <div class="col-span-3">
                <jet-label for="last_name" value="Last Name"/>
                <input id="last_name" type="text" class="block w-full mt-1" v-model="form['last_name']" autofocus required/>
                <jet-input-error :message="form.errors['last_name']" class="mt-2"/>
            </div>
            <div  class="col-span-3">
                <jet-label for="email" value="Email"/>
                <input id="email" type="email" class="block w-full mt-1" v-model="form.email" autofocus/>
                <jet-input-error :message="form.errors.email" class="mt-2"/>
            </div>
            <div  class="col-span-3">
                <jet-label for="mobile_phone" value="Phone"/>
                <input id="mobile_phone" type="text" class="block w-full mt-1" v-model="form['mobile_phone']" autofocus/>
                <jet-input-error :message="form.errors['mobile_phone']" class="mt-2"/>
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
            <Button type="button" @click="$inertia.visit(route('data.leads'))" :class="{ 'opacity-25': form.processing }" error outline :disabled="form.processing">
                Cancel
            </Button>
            <div class="flex-grow" />
            <Button :class="{ 'opacity-25': form.processing }" class="btn-secondary" :disabled="form.processing"  :loading="form.processing">
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import {useForm} from '@inertiajs/inertia-vue3'


import AppLayout from '@/Layouts/AppLayout.vue'
import Button from '@/Components/Button.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'

import JetDropdown from '@/Components/Dropdown'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,

        JetDropdown,
        JetInputError,
        JetLabel,
    },
    props: ['clientId', 'locations'],
    setup(props, context) {
        console.log('props', props);
        let operation = 'Create';
        let lead = {
            'first_name': null,
            'last_name': null,
            email: null,
            'mobile_phone': null,
            'gr_location_id': null,
            client_id: props.clientId
        }

        const form = useForm(lead)

        let handleSubmit = () => form.post(`/data/leads/create`);

        return {form, buttonText: operation, handleSubmit}
    },
}
</script>

<style scoped>

</style>

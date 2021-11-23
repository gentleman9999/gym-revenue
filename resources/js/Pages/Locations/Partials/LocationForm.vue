<template>
    <jet-form-section @submitted="handleSubmit">
        <!--        <template #title>-->
        <!--            Location Details-->
        <!--        </template>-->

        <!--        <template #description>-->
        <!--            {{ buttonText }} a location.-->
        <!--        </template>-->
        <template #form>

                <div class="col-span-6">
                    <jet-label for="name" value="Name"/>
                    <input id="name" type="text" class="block w-full mt-1" v-model="form.name" autofocus/>
                    <jet-input-error :message="form.errors.name" class="mt-2"/>
                </div>
                <div class="col-span-4">
                    <jet-label for="city" value="City"/>
                    <input id="city" type="text" class="block w-full mt-1" v-model="form.city" autofocus/>
                    <jet-input-error :message="form.errors.city" class="mt-2"/>
                </div>
                <div  class="col-span-1">
                    <jet-label for="state" value="State"/>
                    <input id="state" type="text" class="block w-full mt-1" v-model="form.state" autofocus/>
                    <jet-input-error :message="form.errors.state" class="mt-2"/>
                </div>
            <div  class="col-span-1">
                <jet-label for="zip" value="ZIP Code"/>
                <input id="zip" type="text" class="block w-full mt-1" v-model="form.zip" autofocus/>
                <jet-input-error :message="form.errors.zip" class="mt-2"/>
            </div>

                <div class="col-span-6 space-y-2">
                    <jet-label for="address1" value="Address"/>
                    <input id="address1" type="text" class="block w-full mt-1" v-model="form.address1" autofocus/>
                    <jet-input-error :message="form.errors.address1" class="mt-2"/>
                    <input id="address2" type="text" class="block w-full mt-1" v-model="form.address2" autofocus/>
                    <jet-input-error :message="form.errors.address2" class="mt-2"/>
                </div>


                <input id="client_id" type="hidden" v-model="form.client_id"/>
                <jet-input-error :message="form.errors.client_id" class="mt-2"/>
        </template>

        <template #actions>
<!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <Button type="button" @click="$inertia.visit(route('locations'))" :class="{ 'opacity-25': form.processing }" error outline :disabled="form.processing">
                Cancel
            </Button>
            <div class="flex-grow" />
            <Button class="btn-secondary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"  :loading="form.processing">
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

import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,

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
                city: null,
                state: null,
                address1: null,
                address2: null,
                zip: null,
                client_id: props.clientId
            }
            operation = 'Create';
        }

        const form = useForm(location)

        let handleSubmit = () => form.put(`/locations/${location.id}`);
        if (operation === 'Create') {
            handleSubmit = () => form.post('/locations');
        }


        return {form, buttonText: operation, handleSubmit}
    },
}
</script>

<template>
    <jet-form-section @submitted="handleSubmit">
        <!--        <template #title>-->
        <!--            Location Details-->
        <!--        </template>-->

        <!--        <template #description>-->
        <!--            {{ buttonText }} a location.-->
        <!--        </template>-->
        <template #form>

                <div class="col-span-4">
                    <jet-label for="name" value="Name"/>
                    <input id="name" type="text" class="block w-full mt-1" v-model="form.name" autofocus/>
                    <jet-input-error :message="form.errors.name" class="mt-2"/>
                </div>
            <div class="col-span-2">
                <jet-label for="location_no" value="Location Number/ID"/>
                <input id="location_no" type="text" class="block w-full mt-1" v-model="form.location_no" />
                <jet-input-error :message="form.errors.location_no" class="mt-2"/>
            </div>
                <div class="col-span-4">
                    <jet-label for="city" value="City"/>
                    <input id="city" type="text" class="block w-full mt-1" v-model="form.city" />
                    <jet-input-error :message="form.errors.city" class="mt-2"/>
                </div>
                <div  class="col-span-1">
                    <jet-label for="state" value="State" />
                    <v-select id="state" v-model="form.state" :options="optionStates" :reduce="(option) => option.code" label="label"></v-select>
                    <jet-input-error :message="form.errors.state" class="mt-2" />
                </div>
            <div  class="col-span-1">
                <jet-label for="zip" value="ZIP Code"/>
                <input id="zip" type="text" class="block w-full mt-1" v-model="form.zip" />
                <jet-input-error :message="form.errors.zip" class="mt-2"/>
            </div>

                <div class="col-span-6 space-y-2">
                    <jet-label for="address1" value="Address"/>
                    <input id="address1" type="text" class="block w-full mt-1" v-model="form.address1" />
                    <jet-input-error :message="form.errors.address1" class="mt-2"/>
                    <input id="address2" type="text" class="block w-full mt-1" v-model="form.address2" />
                    <jet-input-error :message="form.errors.address2" class="mt-2"/>
                </div>

            <div class="col-span-2 space-y-2">
                <jet-label for="phone" value="Phone"/>
                <input id="phone" type="text" class="block w-full mt-1" v-model="form.phone" />
                <jet-input-error :message="form.errors.phone" class="mt-2"/>
            </div>

            <div class="col-span-2 space-y-2">
                <jet-label for="opendate" value="Open Date"/>
                <DatePicker id="opendate" v-model="form.opendate"  dark />

                <jet-input-error :message="form.errors.opendate" class="mt-2"/>
            </div>
            <div class="col-span-2 space-y-2">
                <jet-label for="closedate" value="Close Date"/>
                <DatePicker id="closedate" v-model="form.closedate"  dark />

                <jet-input-error :message="form.errors.closedate" class="mt-2"/>
            </div>

            <div class="col-span-2 space-y-2">
                <jet-label for="poc_first" value="POC First"/>
                <input id="poc_first" type="text" class="block w-full mt-1" v-model="form.poc_first" />
                <jet-input-error :message="form.errors.poc_first" class="mt-2"/>
            </div>
            <div class="col-span-2 space-y-2">
                <jet-label for="poc_last" value="POC Last"/>
                <input id="poc_last" type="text" class="block w-full mt-1" v-model="form.poc_last" />
                <jet-input-error :message="form.errors.poc_last" class="mt-2"/>
            </div>
            <div class="col-span-2 space-y-2">
                <jet-label for="poc_phone" value="POC Phone"/>
                <input id="poc_phone" type="text" class="block w-full mt-1" v-model="form.poc_phone"  />
                <jet-input-error :message="form.errors.poc_phone" class="mt-2"/>
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

    <!--           {{this.$page.props.phone}} ------
       {{this.$page.props.poc_first}}
    {{this.$page.props.poc_last}}
        {{this.$page.props.poc_phone}}
         {{this.$page.props.opendate}}
         {{this.$page.proosedate}}
-->
  <!--

       {{this.$page.props.locationitems}}  -->
</template>

<script>
import {useForm, usePage} from '@inertiajs/inertia-vue3'


import AppLayout from '@/Layouts/AppLayout'
import Button from '@/Components/Button'
import JetFormSection from '@/Jetstream/FormSection'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import DatePicker from 'vue3-date-time-picker';
import 'vue3-date-time-picker/dist/main.css';
import vSelect from 'vue-select'
import states from "@/Pages/Comms/States/statesOfUnited"

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        DatePicker,
        'v-select': vSelect
    },
    props: ['clientId', 'location','phone','poc_first','poc_last','poc_phone','opendate','closedate','location_no','DatePicker'],
    setup(props, context) {
        const page = usePage();

        let location = props.location;
        let phone = page.props.value.phone;
        let poc_first = page.props.value.poc_first;
        let poc_last = page.props.value.poc_last;
        let poc_phone = page.props.value.poc_phone;
        let opendate = page.props.value.opendate;
        let closedate = page.props.value.closedate;

        let operation = 'Update';
        if (!location) {
            location = {
                name: null,
                city: null,
                state: null,
                address1: null,
                address2: null,
                zip: null,
                phone: null,
                poc_first:null,
                poc_last:null,
                poc_phone:null,
                opendate:null,
                closedate:null,
                location_no:null,
                client_id: props.clientId
            }
            operation = 'Create';
        }else{
                location.phone = phone,
                location.poc_first = poc_first,
                location.poc_last = poc_last,
                location.poc_phone = poc_phone,
                location.opendate = opendate,
                location.closedate = closedate

                let address1 = location.address1;
                let address2 = location.address2;
                location.address1 = address1
                location.address2 = address2
        }

        const form = useForm(location)
        //
    //    form.put(`/locations/${location.id}`);
        let handleSubmit = () => form.put(route('locations.update', location.id));

        if (operation === 'Create') {
            handleSubmit = () => form.post(route('locations.store'));
        }

        return {form, buttonText: operation, handleSubmit, optionStates: states}
    },
}
</script>

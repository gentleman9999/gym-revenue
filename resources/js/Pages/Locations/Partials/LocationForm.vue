<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6 md:col-span-4">
                <jet-label for="name" value="Name" />
                <input
                    id="name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.name"
                    autofocus
                />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
            <div class="col-span-6 md:col-span-2">
                <jet-label for="location_no" value="Location Number/ID" />
                <input
                    id="location_no"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.location_no"
                />
                <jet-input-error
                    :message="form.errors.location_no"
                    class="mt-2"
                />
            </div>
            <div class="col-span-6 md:col-span-4">
                <jet-label for="city" value="City" />
                <input
                    id="city"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.city"
                />
                <jet-input-error :message="form.errors.city" class="mt-2" />
            </div>
            <div class="col-span-3 md:col-span-1">
                <jet-label for="state" value="State" />
                <multiselect
                    id="state"
                    class="mt-1 multiselect"
                    v-model="form.state"
                    :searchable="true"
                    :create-option="true"
                    :options="optionStates"
                    :classes="multiselectClasses"
                />
                <jet-input-error :message="form.errors.state" class="mt-2" />
            </div>
            <div class="col-span-3 md:col-span-1">
                <jet-label for="zip" value="ZIP Code" />
                <input
                    id="zip"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.zip"
                />
                <jet-input-error :message="form.errors.zip" class="mt-2" />
            </div>

            <div class="col-span-6 space-y-2">
                <jet-label for="address1" value="Address" />
                <input
                    id="address1"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.address1"
                />
                <jet-input-error :message="form.errors.address1" class="mt-2" />
                <input
                    id="address2"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.address2"
                />
                <jet-input-error :message="form.errors.address2" class="mt-2" />
            </div>

            <div class="col-span-6 grid grid-cols-6 gap-6">
                <div class="col-span-6 md:col-span-2 space-y-2">
                    <jet-label for="latitude" value="Location Latitude" />
                    <input
                        id="latitude"
                        type="number"
                        step="0.000001"
                        min="-90.000000"
                        max="89.999999"
                        class="block w-full mt-1"
                        v-model="form.latitude"
                    />
                    <jet-input-error
                        :message="form.errors.latitude"
                        class="mt-2"
                    />
                </div>

                <div class="col-span-6 md:col-span-2 space-y-2">
                    <jet-label for="longitude" value="Location Longitude" />
                    <input
                        id="longitude"
                        type="number"
                        step="0.000001"
                        min="-180.000000"
                        max="179.999999"
                        class="block w-full mt-1"
                        v-model="form.longitude"
                    />
                    <jet-input-error
                        :message="form.errors.longitude"
                        class="mt-2"
                    />
                </div>
            </div>

            <!-- <div class="col-span-6 md:col-span-2"></div> -->

            <div class="col-span-6 md:col-span-2 space-y-2">
                <jet-label for="phone" value="Phone" />
                <input
                    id="phone"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.phone"
                />
                <jet-input-error :message="form.errors.phone" class="mt-2" />
            </div>

            <div class="col-span-3 md:col-span-2 space-y-2">
                <jet-label for="open_date" value="Open Date" />
                <DatePicker
                    id="open_date"
                    v-model="form.open_date"
                    dark
                    :month-change-on-scroll="false"
                    :auto-apply="true"
                    :close-on-scroll="true"
                />

                <jet-input-error
                    :message="form.errors.open_date"
                    class="mt-2"
                />
            </div>
            <div class="col-span-3 md:col-span-2 space-y-2">
                <jet-label for="close_date" value="Close Date" />
                <DatePicker
                    id="close_date"
                    v-model="form.close_date"
                    dark
                    :month-change-on-scroll="false"
                    :auto-apply="true"
                    :close-on-scroll="true"
                />

                <jet-input-error
                    :message="form.errors.close_date"
                    class="mt-2"
                />
            </div>

            <div class="col-span-6 md:col-span-2 space-y-2">
                <jet-label for="poc_first" value="POC First" />
                <input
                    id="poc_first"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.poc_first"
                />
                <jet-input-error
                    :message="form.errors.poc_first"
                    class="mt-2"
                />
            </div>
            <div class="col-span-6 md:col-span-2 space-y-2">
                <jet-label for="poc_last" value="POC Last" />
                <input
                    id="poc_last"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.poc_last"
                />
                <jet-input-error :message="form.errors.poc_last" class="mt-2" />
            </div>
            <div class="col-span-6 md:col-span-2 space-y-2">
                <jet-label for="poc_phone" value="POC Phone" />
                <input
                    id="poc_phone"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.poc_phone"
                />
                <jet-input-error
                    :message="form.errors.poc_phone"
                    class="mt-2"
                />
            </div>
            <div class="col-span-6 md:col-span-2">
                <jet-label for="location_type" value="Location Types" />
                <multiselect
                    id="location_type"
                    class="mt-1 multiselect"
                    v-model="form.location_type"
                    :searchable="true"
                    :options="optionLocationTypes"
                    :classes="multiselectClasses"
                />
                <jet-input-error
                    :message="form.errors.location_type"
                    class="mt-2"
                />
            </div>

            <input id="client_id" type="hidden" v-model="form.client_id" />
            <jet-input-error :message="form.errors.client_id" class="mt-2" />
        </template>

        <template #actions>
            <!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <Button
                type="button"
                @click="$inertia.visit(route('locations'))"
                :class="{ 'opacity-25': form.processing }"
                error
                outline
                :disabled="form.processing"
            >
                Cancel
            </Button>
            <div class="flex-grow" />
            <Button
                class="btn-secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="!isFormValid"
                :loading="form.processing"
            >
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import { computed } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { useGymRevForm } from "@/utils";

import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import DatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses } from "@/utils";
import states from "@/Pages/Comms/States/statesOfUnited";
import { transformDate } from "@/utils/transformDate";
import { parseLocationTypeDisplayName } from "@/utils/locationTypeEnum";

export default {
    components: {
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        DatePicker,
        multiselect: Multiselect,
    },
    props: [
        "clientId",
        "location",
        "phone",
        "poc_first",
        "poc_last",
        "poc_phone",
        "open_date",
        "close_date",
        "location_no",
        "locationTypes",
    ],
    setup(props, context) {
        const page = usePage();

        let location = props.location;
        let poc_first = props.poc_first;
        let poc_last = props.poc_last;
        let poc_phone = props.poc_phone;
        let locationTypes = props.locationTypes;

        let operation = "Update";
        if (!location) {
            location = {
                name: "",
                city: "",
                state: "",
                address1: "",
                address2: "",
                zip: "",
                phone: "",
                poc_first: "",
                poc_last: "",
                poc_phone: "",
                open_date: null,
                close_date: null,
                location_no: "",
                client_id: props.clientId,
                location_type: "",
                latitude: null,
                longitude: null,
            };
            operation = "Create";
        } else {
            location.phone = location.phone;
            location.poc_first = poc_first;
            location.poc_last = poc_last;
            location.poc_phone = poc_phone;
            location.open_date = location.open_date;
            location.close_date = location.close_date;
            location.address1 = location.address1;
            location.address2 = location.address2;
            location.latitude = location.latitude;
            location.longitude = location.longitude;
        }

        const transformData = (data) => ({
            ...data,
            open_date: transformDate(data.open_date),
            close_date: transformDate(data.close_date),
        });

        const form = useGymRevForm(location);

        const isFormValid = computed(
            () => form.isDirty && form.location_type.trim()?.length
        );

        //
        //    form.put(`/locations/${location.id}`);
        let handleSubmit = () =>
            form
                .dirty()
                .transform(transformData)
                .put(route("locations.update", location.id));

        if (operation === "Create") {
            handleSubmit = () =>
                form.transform(transformData).post(route("locations.store"));
        }

        let optionsStates = [];
        for (let x in states) {
            optionsStates.push(states[x].abbreviation);
        }

        return {
            form,
            buttonText: operation,
            handleSubmit,
            optionStates: optionsStates,
            multiselectClasses: getDefaultMultiselectTWClasses(),
            isFormValid,
            optionLocationTypes: locationTypes.map(function (locationType) {
                return {
                    value: locationType.value,
                    label: parseLocationTypeDisplayName(locationType),
                };
            }),
        };
    },
};
</script>

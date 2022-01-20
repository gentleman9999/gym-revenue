<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Locations {{this.$page.props.role}}</h2>

        </template>

        <gym-revenue-crud
            base-route="locations"
            model-name="Location"
            :fields="fields"
            :resource="locations"
            :actions="{
               trash:{
                   label: 'CLose Club',
                    handler: ({data}) => handleClickTrash(data.id)
                },


            }"
        ><!--base-route="locations"-->
        <div class="block py-2 text-xs text-gray-400">Closed:</div>
        <select
            v-model="form.trashed"
            class="mt-1 w-full form-select"
        >
            <option :value="null" />
            <option value="with">With Closed</option>
            <option value="only">Only Closed</option>
        </select>
<!--Filters to Add
        <div  class="block py-2 text-xs text-gray-400">State:</div>
        <select
            v-model="form.state"
            class="mt-1 w-full form-select"
        >
            <option :value="null" />
            <option v-for="(state, i) in this.$page.props.locations" :value="grlocations.gymrevenue_id">{{grlocations.name }}
            </option>
        </select>
            -->

        </gym-revenue-crud>
 <!--      {{this.$page.props}} -->

        <confirm
            title="Really Close This Club?"
            v-if="confirmTrash"
            @confirm="handleConfirmTrash"
            @cancel="confirmTrash = null"
        >
            Are you sure you want to Close this Club?<BR/>

        </confirm>




    </app-layout>
</template>

<script>
import { defineComponent,ref } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
//import {ref} from "vue/dist/vue";
import {Inertia} from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm";

import Button from "@/Components/Button";
import JetBarContainer from "@/Components/JetBarContainer";
export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
    },
    props: ["sessions", "locations", "title", "isClientUser", "filters"],
    setup(props) {
        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        }; handleClickTrash()
        const handleConfirmTrash = () => {
            /* */

            axios.delete(route("locations.trash", confirmTrash.value)).then(response => {
                    setTimeout(() => response($result, 200),10000)
                },
                Inertia.reload(),
                location.reload(),
                confirmTrash.value = null
            );
        };
        return { handleClickTrash, confirmTrash, handleConfirmTrash, Inertia }; //, fields
    },
    computed: {
        fields() {
            if (this.isClientUser) {
                return ["name", "city", "state", "active"];
            }
            return [
                { name: "client.name", label:"client"},
                "name",
                "city",
                "state",
                "active",
            ];
        },
    },

});
</script>

<template>
    <app-layout :title="title">
        <template #header>
            <div class="text-center">
                <h2 class="font-semibold text-xl  leading-tight">
                    SMS Campaigns Management
                </h2>
                <small></small>
            </div>
        </template>
    </app-layout>
</template>

<script>
import {defineComponent} from "vue";
import {Link} from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout.vue'
import JetDropdown from '@/Jetstream/Dropdown'
import JetDropdownLink from '@/Jetstream/DropdownLink'
import JetBarContainer from "@/Components/JetBarContainer";

export default defineComponent({
    name: "SMSCampaignsIndex",
    components: {
        Link,
        AppLayout,
        JetDropdown,
        JetDropdownLink,
        JetBarContainer
    },
    props: ['title'],
    setup(props) {},
    watch: {},
    data() {
        return {};
    },
    computed: {

    },
    methods: {

    },
    mounted() {}
});
</script>

<style scoped>

</style>

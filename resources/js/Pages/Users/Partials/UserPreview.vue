<template>
    <div class="grid grid-cols-6 gap-6">
        <div class="form-control col-span-6 lg:col-span-3">
            <jet-label for="first_name" value="First Name"/>
            <input
                id="first_name"
                type="text"
                class="block w-full mt-1"
                :value="user.first_name"
                disabled
            />
        </div>
        <div class="form-control col-span-6 lg:col-span-3">
            <jet-label for="last_name" value="Last Name"/>
            <input
                id="last_name"
                type="text"
                class="block w-full mt-1"
                :value="user.last_name"
                disabled
            />
        </div>
        <div class="form-control col-span-6 lg:col-span-3">
            <jet-label for="email" value="Email"/>
            <input
                id="email"
                type="email"
                class="block w-full mt-1"
                :value="user.email"
                disabled
            />
        </div>

        <div class="form-control col-span-6 lg:col-span-3">
            <jet-label for="phone" value="Phone"/>
            <input id="phone" type="tel" class="block w-full mt-1" :value="user.phone" disabled />
        </div>
        <div class="form-control col-span-6 lg:col-span-3" v-if="user.security_role">
            <jet-label for="role" value="Security Role"/>
            <input
                type="text"
                id="role"
                class="block w-full mt-1"
                :value="user.security_role.security_role"
                disabled
            />
        </div>
    </div>
</template>

<script>
import {useForm, usePage} from "@inertiajs/inertia-vue3";

import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetLabel from "@/Jetstream/Label";

export default {
    components: {
        Button,
        JetFormSection,
        JetLabel,
    },
    props: ["user"],
    setup(props, {emit}) {
        const page = usePage();
        let user = props.user;
        let phone = ((user !== undefined)
            && ('phone_number' in user)
            && (user['phone_number'])
            && ('value' in user['phone_number'])
        ) ? user['phone_number'].value : null;

        if (user) {
            user.phone = phone;
        }


        return {user};
    },
};
</script>

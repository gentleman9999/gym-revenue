<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="form-control col-span-6">
                <jet-label for="name" value="Name"/>
                <input
                    id="name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.name"
                    autofocus
                />
                <jet-input-error :message="form.errors.name" class="mt-2"/>
            </div>
            <div class="form-control col-span-6">
                <jet-label for="email" value="Email"/>
                <input
                    id="email"
                    type="email"
                    class="block w-full mt-1"
                    v-model="form.email"
                    autofocus
                />
                <jet-input-error :message="form.errors.email" class="mt-2"/>
            </div>

            <div class="form-control col-span-6">
                <jet-label for="phone" value="Phone"/>
                <input id="phone" type="text"  class="block w-full mt-1"  v-model="form.phone"  autofocus/>
                <jet-input-error :message="form.errors.phone" class="mt-2"/>
            </div>

            <div class="form-control col-span-6" v-if="operation==='Create'">
                <jet-label for="password" value="Password"/>
                <input
                    id="password"
                    type="password"
                    class="block w-full mt-1"
                    v-model="form.password"
                />
                <jet-input-error :message="form.errors.password" class="mt-2"/>
            </div>
            <div class="form-control col-span-6">
                <jet-label for="role" value="Role"/>
                <select
                    id="role"
                    class="block w-full mt-1"
                    v-model="form.role"
                >
                    <option>Admin</option>
                    <option>Location Manager</option>
                    <option>Regional Admin</option>
                    <option>Sales Rep</option>
                    <option>Member</option>
                </select>
                <jet-input-error :message="form.errors.role" class="mt-2"/>
            </div>
        </template>

        <template #actions>
            <Button type="button" @click="$inertia.visit(route('users'))" :class="{ 'opacity-25': form.processing }"
                    error outline :disabled="form.processing">
                Cancel
            </Button>
            <div class="flex-grow"/>
            <Button class="btn-secondary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                    :loading="form.processing">
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import {useForm, usePage} from "@inertiajs/inertia-vue3";

import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: ["clientId", "user","phone"],
    emits: ['success'],
    setup(props, {emit}) {
        const page = usePage();
        let phone =  usePage().props.value.phone;

        let user = props.user;

        let operation = 'Update';
        if (user) {
            const team_id = page.props.value.user.current_team_id;
            const team_idx = user.teams.findIndex(team=>team.id === team_id);
            user.phone = phone,
            user.role = user.teams[team_idx].pivot.role
        } else {
            user = {
                name: null,
                email: null,
                phone:null,
                password: null,
                role: null,
                client_id: props.clientId
            }
            operation = 'Create';
        }

        const form = useForm(user);

        let handleSubmit = () => form.put(route('users.update', user.id));
        if (operation === 'Create') {
            handleSubmit = () => form.post(route('users.store'));
        }

        return {form, buttonText: operation, operation, handleSubmit};
    },
};
</script>

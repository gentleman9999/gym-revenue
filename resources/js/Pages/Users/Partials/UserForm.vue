<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <!-- First Name -->
            <div class="form-control col-span-4">
                <jet-label for="fname" value="First Name"/>
                <input
                    id="first_name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.first_name"
                    autofocus
                />
                <jet-input-error :message="form.errors.first_name" class="mt-2" />
            </div>
            <!-- Last Name -->
            <div class="form-control col-span-4">
                <jet-label for="name" value="Last Name"/>
                <input
                    id="last_name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.last_name"
                    autofocus
                />
                <jet-input-error :message="form.errors.first_name" class="mt-2" />
            </div>
            <!-- (Work) Email -->
            <div class="form-control col-span-4">
                <jet-label for="email" value="Work Email"/>
                <input
                    id="email"
                    type="email"
                    class="block w-full mt-1"
                    v-model="form.email"
                    autofocus
                />
                <jet-input-error :message="form.errors.email" class="mt-2"/>
            </div>

            <!-- Personal Email -->
            <div class="form-control col-span-4">
                <jet-label for="email" value="Personal Email"/>
                <input
                    id="altEmail"
                    type="email"
                    class="block w-full mt-1"
                    v-model="form.altEmail"
                    autofocus
                />
                <jet-input-error :message="form.errors.altEmail" class="mt-2"/>
            </div>

            <!-- Address 1 -->
            <div class="form-control col-span-9">
                <jet-label for="address1" value="Home Address"/>
                <input
                    id="address1"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.address1"
                    autofocus
                />
                <jet-input-error :message="form.errors.address1" class="mt-2"/>
            </div>
            <!-- Address 2 -->
            <div class="form-control col-span-9">
                <jet-label for="address2" value="Home Address (cont)"/>
                <input
                    id="address2"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.address2"
                    autofocus
                />
                <jet-input-error :message="form.errors.address2" class="mt-2"/>
            </div>
            <!-- City -->
            <div class="form-control col-span-3">
                <jet-label for="city" value="City"/>
                <input
                    id="city"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.city"
                    autofocus
                />
                <jet-input-error :message="form.errors.city" class="mt-2"/>
            </div>
            <!-- State -->
            <div class="form-control col-span-3">
                <jet-label for="state" value="State"/>
                <input
                    id="state"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.state"
                    maxlength="2"
                    @keyup="upperCaseF(form.state)"
                    autofocus
                />
                <jet-input-error :message="form.errors.state" class="mt-2"/>
            </div>
            <!-- Zip -->
            <div class="form-control col-span-3">
                <jet-label for="zip" value="Zip Code"/>
                <input
                    id="zip"
                    type="number"
                    class="block w-full mt-1"
                    v-model="form.zip"
                    autofocus
                />
                <jet-input-error :message="form.errors.zip" class="mt-2"/>
            </div>

            <!-- Official Position -->
            <div class="form-control col-span-3">
                <jet-label for="jobTitle" value="Official Position"/>
                <input
                    id="jobTitle"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.jobTitle"
                    autofocus
                />
                <jet-input-error :message="form.errors.jobTitle" class="mt-2"/>
            </div>

            <!-- Contact Phone # -->
            <div class="form-control col-span-3">
                <jet-label for="phone" value="Contact Phone"/>
                <input id="phone" type="tel" class="block w-full mt-1"  v-model="form.phone"  autofocus/>
                <jet-input-error :message="form.errors.phone" class="mt-2"/>
            </div>

            <!-- Security Role -->
            <div class="form-control col-span-3" v-if="clientId">
                <jet-label for="role" value="Security Role"/>
                <select
                    id="role"
                    class="block w-full mt-1"
                    v-model="form.security_role"
                >
                    <option
                        v-for="{ security_role, id } in securityRoles"
                        :value="id"
                    >
                        {{ security_role }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors.security_role"
                    class="mt-2"
                />
            </div>


            <!-- Start Date -->

            <div class="form-control col-span-3">
                <jet-label for="start_date" value="Date / Start of Work"/>
                <DatePicker v-model="form['start_date']"  dark />
                <jet-input-error :message="form.errors.start_date" class="mt-2"/>
            </div>

            <!-- End Date -->
            <div class="form-control col-span-3">
                <jet-label for="end_date" value="Date / End of Work"/>
                <DatePicker v-model="form['end_date']"  dark />
                <jet-input-error :message="form.errors.end_date" class="mt-2"/>
            </div>


            <!-- Termination Date -->
            <div class="form-control col-span-3">
                <jet-label for="Termination_date" value="Date of Termination"/>
                <DatePicker v-model="form['Termination_date']"  dark />
                <jet-input-error :message="form.errors.Termination_date" class="mt-2"/>
            </div>


            <!-- Notes -->
            <div class="form-control col-span-9">
                <jet-label for="notes" value="Notes"/>
                <textarea v-model="form['notes']"  dark  rows="5" cols="33"/>
                <jet-input-error :message="form.errors.notes" class="mt-2"/>
            </div>

       </template>

       <template #actions>
           <Button
               type="button"
               @click="$inertia.visit(route('users'))"
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
               :disabled="form.processing"
               :loading="form.processing"
           >
               {{ buttonText }}
           </Button><br/><br/><br/><br/>
       </template>
   </jet-form-section>
</template>

<script>
import { useForm, usePage } from "@inertiajs/inertia-vue3";

import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import DatePicker from 'vue3-date-time-picker';
import 'vue3-date-time-picker/dist/main.css';

export default {
   components: {
       AppLayout,
       Button,
       JetFormSection,
       JetInputError,
       JetLabel,
       DatePicker,
   },
   props: ["clientId", "user", "clientName"],
   emits: ["success"],
   setup(props, { emit }) {
       const page = usePage();
       let user = props.user;
       const securityRoles = page.props.value.securityRoles;


       const team_id = page.props.value.user.current_team_id;
       let phone = ((user !== undefined)
           && ('phone_number' in user)
           && (user['phone_number'])
           && ('value' in user['phone_number'])
       ) ? user['phone_number'].value : null;

       let operation = "Update";
       if (user) {
           user.security_role =
               user?.details?.find((detail) => detail.name === "security_role")
                   ?.value || null;
           user.team_id = team_id;
           user.first_name = user["first_name"];
           user.last_name = user["last_name"];
           user.altEmail = (('alt_email' in user) && (user['alt_email'] !== null)) ? user['alt_email'].value ?? '' : '';
           user.address1 = (('address1' in user)  && (user['address1'] !== null)) ? user['address1'].value : '';
           user.address2 = (('address2' in user)  && (user['address2'] !== null)) ? user['address2'].value : '';
           user.city     = (('city' in user) && (user['city'] !== null)) ? user['city'].value : '';
           user.state    = (('state' in user) && (user['state'] !== null)) ? user['state'].value : '';
           user.zip      = (('zip' in user) && (user['zip'] !== null)) ? user['zip'].value : '';
           user.jobTitle = (('job_title' in user) && (user['job_title'] !== null)) ? user['job_title'].value : '';
           user.phone = phone;
           user.security_role =
               user?.details?.find((detail) => detail.name === "security_role")
                   ?.value || null;
           console.log({ user });

       } else {
           user = {
               first_name: "",
               last_name: "",
               email: '',
               altEmail: '',
               security_role: '',
               phone:'',
               address1: '',
               address2: '',
               city: '',
               team_id,
       state: '',
               zip: '',
               jobTitle: '',
               client_id: props.clientId
           };
           //only add clientId when applicable to make user validation rules work better
           if (props.clientId) {
               user.client_id = props.clientId;
           }
           operation = "Create";
       }

       const form = useForm(user);
       let upperCaseF = (text) => {
           form.state = text.toUpperCase();
       };

       let handleSubmit = () => form.put(route("users.update", user.id));
       if (operation === "Create") {
           handleSubmit = () => form.post(route("users.store"));
       }

       return {
           form,
           buttonText: operation,
           operation,
           handleSubmit,
           securityRoles,
       upperCaseF
       };
   },
};
</script>

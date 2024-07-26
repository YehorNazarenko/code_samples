<script>
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import staticData from "@/mixins/staticData.js";
import fillRule from "@/mixins/fillRule.js";

export default {
    components: {DashboardLayout},
    mixins: [staticData, fillRule],
    props: {
        user: Object
    },
    mounted() {
        this.email = this.user.email;
        this.name = this.user.name;
    },
    data() {
        return {
            email: '',
            name: '',
            message: '',
            isFormValid: null,
            dialog: {
                show: false,
            },
        }
    },
    methods: {
        submit() {
            if (!this.isFormValid) {
                return;
            }
            this.$inertia.post('support', {
                email: this.email,
                name: this.name,
                message: this.message,
            }, {
                onSuccess: () => {
                    this.message = '';
                    this.dialog.show = true;
                    this.$nextTick(() => {
                        this.$refs.form.resetValidation();
                    });
                },
            })
        },
    }
}
</script>

<template>
    <DashboardLayout>
        <template #pageName>Support</template>
        <template #default>
            <v-container>
                <v-row>
                    <v-col cols="6" class="mx-auto">
                        <v-card-text
                            variant="flat"
                            class="border rounded-2xl overflow-hidden my-6 px-4 py-6 bg-white bordered"
                        >
                            <v-form ref="form" v-model="isFormValid" lazy-validation @submit.prevent="submit">
                                <v-text-field
                                    v-model="email"
                                    type="email"
                                    variant="solo"
                                    class="mt-1 block w-full"
                                    :rules="fillRules"
                                    placeholder="Your email"
                                ></v-text-field>
                                <v-text-field
                                    v-model="name"
                                    type="text"
                                    variant="solo"
                                    class="mt-1 block w-full"
                                    :rules="fillRules"
                                    placeholder="Your Name"
                                ></v-text-field>

                                <v-textarea
                                    id="message"
                                    name="message"
                                    v-model="message"
                                    variant="solo"
                                    class="mt-1 block w-full"
                                    placeholder="Your message"
                                    :rules="fillRules"
                                    auto-grow
                                ></v-textarea>

                                <div class="d-flex justify-end">
                                    <v-btn type="submit" variant="tonal">
                                        Send
                                    </v-btn>
                                </div>
                            </v-form>
                        </v-card-text>
                    </v-col>
                </v-row>
            </v-container>
            <v-dialog
                v-model="dialog.show"
                width="auto"
            >
                <v-card class="mx-auto">
                    <v-card-text class="px-4 py-4">
                        <v-card-text>
                            Your message has been sent successfully.
                        </v-card-text>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="primary" class="ml-auto" @click="dialog.show = false">Ok</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
    </DashboardLayout>
</template>

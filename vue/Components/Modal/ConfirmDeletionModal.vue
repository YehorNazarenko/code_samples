<script>
import Modal from "@/Components/Modal/Modal.vue";

export default {
    components: {Modal},
    props: {
        data: Object
    },
    methods: {
        close() {
            this.$emit('close');
        }
    }
}
</script>

<template>
    <Modal
        :show="data.show"
        @close="close"
        :max-width="'2xl'"
    >

        <v-row>
            <v-col>
                <div class="px-6 py-4">
                    <div class="text-h6 font-medium text-gray-900 mb-5">
                        {{ data.title }}
                    </div>
                    <p>Are you sure you want to delete <i>{{ data.text }}</i>?</p>
                </div>

            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
                    <v-btn type="submit" class="me-3" color="red" @click="$emit('confirm')">
                        Delete
                    </v-btn>
                    <v-btn variant="flat" @click="close">
                        Cancel
                    </v-btn>

                </div>
            </v-col>
        </v-row>
    </Modal>
</template>

<style>

</style>

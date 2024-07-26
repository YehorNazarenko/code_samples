<script>
import ConfirmDeletionModal from "@/Components/Modal/ConfirmDeletionModal.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import SvgIcon from "@jamescoyle/vue-icon";
import staticData from "@/mixins/staticData.js";
import VehicleEditModal from "@/Components/Modal/VehicleEditModal.vue";
import permission from "@/mixins/permission.js";
import moment from "moment";
import LiveVideoFeedModal from "@/Components/Modal/LiveVideoFeedModal.vue";
import {router, usePage} from "@inertiajs/vue3";
import MachineTypeImage from "@/Components/MachineTypeImage.vue";
import {computed} from "vue";
import map from "@/mixins/map.js";
import ImportFleetsModal from "@/Components/Modal/ImportFleetsModal.vue";
import ImportItemsModal from "@/Components/Modal/ImportItemsModal.vue";
import fileHelper from "@/mixins/fileHelper.js";
import query from "@/mixins/query.js";
import CustomBadge from "@/Components/CustomBadge.vue";
import DeviceIssuesModal from "@/Components/Modal/DeviceIssuesModal.vue";
import cachedPages from "@/mixins/cachedPages.js";
import _omit from "lodash/omit.js";
import IconWithTooltip from "@/Components/IconWithTooltip.vue";

export default {
    components: {
        IconWithTooltip,
        DeviceIssuesModal,
        CustomBadge,
        ImportItemsModal,
        ImportFleetsModal,
        MachineTypeImage, LiveVideoFeedModal, VehicleEditModal, ConfirmDeletionModal, DashboardLayout, SvgIcon},
    mixins: [staticData, permission, map, fileHelper, query, cachedPages],
    props: {
        fleets: Array,
        Objects: Array,
        ObjectsForFilter: Array,
        fleetsForFilter: Array,
        itemsData: Array,
        markers: Array,
        params: Array,
        vehicleTypes: Array,
        itemsModels: Array,
        devices: Array,
        itemsExperiencingIssues: Array,
    },
    watch: {
        itemsData(data) {
            const fetchUrl = this.getFetchUrl();
            this.pagesData[fetchUrl] = data;
            this.items = data;
            this.pagination = _omit(data, ['data']);
        }
    },
    data() {
        return {
            availableFilters: ['site','fleet','vehicle_type','search'],
            items: this.itemsData.data,
            pagination: _omit(this.itemsData, ['data']),
            markers: this.markers,
            center: computed(() => this.getCenter()),
            headers: [
                {
                    title: '',
                    key: 'item_type',
                    sortable: false
                },
                {
                    title: 'Registration',
                    key: 'registration',
                    sortable: false
                },
                {
                    title: "UUID(s)",
                    key: 'devices',
                    sortable: false
                },
                {
                    title: 'Company',
                    key: 'fleet.company.title',
                    sortable: false,
                },
            ],
            deleteObject: {
                id: 0,
                title: 'Delete Vehicle',
                text: '',
                show: false
            },
            vehicleItem: {
                show: false,
                registration: null,
                machine_type: null,
                fleet_id: null,
                site_id: null,
                enabled: true
            },
            filters: {
                site: '',
                fleet: '',
                vehicle_type: 'All Types',
                search: ''
            },
            videoFeedItem: {
                show: false,
            },
            showCsvUploadModal: false,
            vehicleAllTypes: [],
            currentCompany: computed(() => usePage().props.current_company),
            selectedItem: {},
            selectedItemPosition: null,
            vehicleIssuesModal: {
                show: false,
            }
        }
    },
    created() {
        this.vehicleAllTypes = ['All Types', ...this.vehicleTypes, ...this.machineTypes]
            .filter((type, index, array) => !!type && type !== 'Other (Please enter)' && array.indexOf(type) === index)
        this.availableFilters.forEach(filterName => {
            if(this.params.filter?.[filterName]) {
                this.filters[filterName] = +this.params.filter[filterName];
            }
        })
    },
    methods: {
        addVehicle() {
            this.vehicleItem.show = true;
        },
        editVehicle(item) {
            this.vehicleItem = {
                ...item,
                device_ids: item.devices.map(device => device.id),
                show: true
            };
        },
        deleteVehicle(item) {
            this.deleteObject.id = item.id;
            this.deleteObject.show = true;
            this.deleteObject.text = item.registration;
        },
        confirmDeletion() {
            this.$inertia.delete(`items/${this.deleteObject.id}`, {
                onSuccess: () => {
                    this.closeDeletionModal();
                }
            });
        },
        closeDeletionModal() {
            this.deleteObject.id = 0;
            this.deleteObject.show = false;
        },
        closeEditModal() {
            this.vehicleItem = {
                show: false,
                registration: null,
                fleet_id: null,
                site_id: null,
                enabled: true,
                device_ids: [],
            };
        },
        getFetchUrl() {
            const {pathname} = window.location;

            const params = {
                page:this.pagination.current_page,
                per_page:this.pagination.per_page
            };
            const filters = {};
            for (const key in this.filters) {
                if (this.filters[key] !== 'All Types') {
                    filters[key] = this.filters[key]
                }}

            const queryParams = this.makeQueryString({filters, params})
            return queryParams ? `${pathname}?${queryParams}` : `${pathname}`
        },
        onFilterChange() {
            const fetchUrl = this.getFetchUrl();

            const res = this.getCachedPageData(fetchUrl);
            if (res) {
                this.items = res;
                this.pagination = _omit(res, ['data'])
            }
        },
        isOnline(date) {
            return date ? moment().diff(moment.utc(date), 'minutes', true) <= 10 : false
        },
        showLiveVideoFeedModal(item) {
            const device = item.devices?.length ? item.devices[0] : null

            this.videoFeedItem = {
                deviceName: item.registration,
                lastOnline: device?.last_online ?? '',
                cameras: device ?
                    [
                      {name: 'cam 1', channel: 1, deviceId: device.id},
                      {name: 'cam 2', channel: 2, deviceId: device.id},
                      {name: 'cam 3', channel: 3, deviceId: device.id},
                      {name: 'cam 4', channel: 4, deviceId: device.id},
                    ] : [],
                show: true
            }
        },
        closeLiveVideoFeedModal() {
            this.videoFeedItem.show = false
        },
        viewVehicle(id) {
            router.get(route('items.show', id));
        },
        setShowCsvUploadModal() {
            this.showCsvUploadModal = !this.showCsvUploadModal;
        },
        exportItems() {
            this.downloadCsv('/items/export', this.generateCsvFileName('items'))
        },
        getVehicleHeaders() {
            return this.headers.filter(item => item.key === 'fleet.company.title'
                ?  (this.currentCompany.has_subcompanies && this.currentCompany.show_data_from_children)
                : true
            )
        },
        handleRowClick(event, {item}) {
            const selectedItem = this.selectedItem.id === item.id ? {} : item;
            const ping = this.markers.find(m => m.id === selectedItem.id);

            this.selectedItem = selectedItem
            this.selectedItemPosition = ping?.position?.lat && ping?.position?.lng
                ? ping.position
                : null
        },
        handleShowDeviceIssuesModal() {
            const issues = [];
            this.itemsExperiencingIssues.forEach(item => {
                const deviceIssues = item.device_issues.map(issue => ({...issue, vehicleRegistration: item.registration}))
                issues.push(...deviceIssues)
            })
            this.vehicleIssuesModal = {
                show: true,
                issues,
            }
        }
    }
}
</script>

<template>
    <DashboardLayout>
        <template #pageName>Items</template>
        <template #default>
            <v-container>
                <GMapMap
                    :center="selectedItemPosition ?? center"
                    :zoom="selectedItemPosition ? 18 : markers?.length ? 6 : 5"
                    :key="selectedItem"
                    map-type-id="terrain"
                    class="w-100 rounded-2xl overflow-hidden "
                    style="height: 40vh"
                >
                    <GMapCluster
                        :zoomOnClick="true"
                        class="cluster-qwe"
                        :styles="gMapStyles"
                    >
                        <GMapMarker
                            :key="index"
                            v-for="(m, index) in markers"
                            :position="m.position"
                            :data-id="m.machineType"
                            :icon="{
                            url: getMachineTypeImageUrl(m.machineType),
                            scaledSize: {width: 35, height: 35},
                          }"
                        >
                        </GMapMarker>
                    </GMapCluster>
                </GMapMap>
            </v-container>
            <v-container v-if="itemsExperiencingIssues.length">
                <v-row>
                    <v-col cols="12">
                        <v-alert
                            type="error"
                            title="Warning"
                            icon="mdi-alert-outline"
                            :text="`There are ${itemsExperiencingIssues.length} items experiencing issues, click here for details`"
                            @click="handleShowDeviceIssuesModal"
                        >
                        </v-alert>
                    </v-col>
                </v-row>
            </v-container>

            <v-container>
                <div class="bg-white bordered rounded-2xl">
                    <v-row>
                        <v-col>
                            <div class="d-flex justify-end px-4">
                                <v-btn
                                    v-if="canCreate('vehicle')"
                                    class="rounded-10 bordered"
                                    color="amber-lighten-1"
                                    height="40"
                                    @click="addVehicle"
                                >
                                    <span class="hidden-xs-only no-upper">Add vehicle</span>
                                    <svg-icon type="mdi" :path="getIcon('plus')" class="ml-2" size="20px"></svg-icon>
                                </v-btn>
                                <v-btn
                                    v-if="canImportCsv('vehicle')"
                                    class="rounded-10 bordered ml-2"
                                    color="amber-lighten-1"
                                    height="40"
                                    @click="setShowCsvUploadModal"
                                >
                                    <span class="hidden-xs-only no-upper">import from csv</span>
                                    <svg-icon type="mdi" :path="getIcon('plus')"></svg-icon>
                                </v-btn>
                                <v-btn
                                    v-if="canExportCsv('vehicle')"
                                    class="rounded-10 bordered ml-2"
                                    color="amber-lighten-1"
                                    height="40"
                                    @click="exportItems"
                                >
                                    <span class="hidden-xs-only no-upper">export to csv</span>
                                    <svg-icon type="mdi" :path="getIcon('plus')"></svg-icon>
                                </v-btn>
                            </div>
                        </v-col>
                        <v-col cols="12">
                            <div class="d-flex justify-space-between px-4">
                                <v-text-field
                                    v-model="filters.search"
                                    variant="solo"
                                    :append-inner-icon="'mdi-magnify'"
                                    @click:append-inner="onFilterChange"
                                    placeholder="Registration"
                                    :hide-details="true"
                                    class="mr-4"
                                ></v-text-field>
                                <v-select
                                    :items="[{id: '', title: 'All Objects', disable: true}, ...ObjectsForFilter]"
                                    v-model="filters.site"
                                    :item-title="item => item.company && currentCompany.show_data_from_children ? `${item.company.title} > ${item.title}` : item.title"
                                    item-value="id"
                                    label="Select site"
                                    class="input-field mr-5 w-20"
                                    append-icon="fa-solid fa-chevron-down"
                                    @update:modelValue="onFilterChange"
                                    hide-details
                                    variant="solo"></v-select>
                                <v-select
                                    :items="[{id: '', title: 'All Fleets', disabled: false},...fleetsForFilter]"
                                    v-model="filters.fleet"
                                    :item-title="item => item.company && currentCompany.show_data_from_children ? `${item.company.title} > ${item.title}` : item.title"
                                    item-value="id"
                                    label="Select fleet"
                                    class="input-field mr-5 w-20"
                                    append-icon="fa-solid fa-chevron-down"
                                    @update:modelValue="onFilterChange"
                                    hide-details
                                    variant="solo"
                                ></v-select>
                                <v-select
                                    :items="vehicleAllTypes"
                                    v-model="filters.vehicle_type"
                                    label="Select machine type"
                                    class="input-field w-20"
                                    append-icon="fa-solid fa-chevron-down"
                                    @update:modelValue="onFilterChange"
                                    hide-details
                                    variant="solo"
                                ></v-select>
                            </div>
                        </v-col>
                    </v-row>
                </div>
            </v-container>


            <v-container>
                <div>
                    <v-row>
                        <v-col>
                            <v-card-text
                                variant="flat"
                                class="border rounded-2xl overflow-hidden"
                            >
                                <v-data-table-server
                                    v-model:items-per-page="pagination.per_page"
                                    v-model:page="pagination.current_page"
                                    :headers="getVehicleHeaders()"
                                    :items="items.data"
                                    :items-length="pagination.total"
                                    @click:row="handleRowClick"
                                    @update:options="onFilterChange"
                                    :items-per-page-options=[10,25,50,100]
                                >
                                    <template v-slot:item.registration="{ item }">
                                        <a :href="route('items.show', [item.id])">{{ item.registration }}</a>
                                    </template>
                                    <template v-slot:item.devices="{ item }">
                                        {{item.devices.map(i => i.uuid).join('; ')}}
                                    </template>
                                    <template v-slot:item.machine_type="{ item }">
                                        <div class="d-flex align-center">
                                            <MachineTypeImage
                                                width="30"
                                                :type="item.machine_type"
                                                :title="item.machine_type"
                                            />
                                        </div>
                                    </template>
                                    <template v-slot:item.last_online="{ item }">
                                        <div class="d-flex">
                                            {{formatTime(getMaxTime(item.devices, 'last_online'))}}
                                            <svg-icon v-if="isOnline(item.devices[0]?.last_online)" type="mdi"
                                                      :path="getIcon('online')" class="text-green my-auto ml-3"
                                                      size="20"></svg-icon>
                                            <!---->
                                        </div>
                                    </template>
                                    <template v-slot:item.unacknowledged_issues_count="{ item }">
                                        <div class="d-flex">
                                            <CustomBadge
                                                v-if="item.unacknowledged_issues_count"
                                                :value="item.unacknowledged_issues_count"
                                                iconType="alert"
                                            />
                                        </div>
                                    </template>
                                    <template v-slot:item.enabled="{ item }">
                                        <v-checkbox-btn
                                            v-model="item.enabled"
                                            :disabled="true"
                                        ></v-checkbox-btn>
                                    </template>
                                    <template v-slot:item.on_hire="{ item }">
                                        <v-checkbox-btn
                                            v-model="item.on_hire"
                                            :disabled="true"
                                        ></v-checkbox-btn>
                                    </template>
                                    <!---->
                                    <template v-slot:item.buttons="{ item }">
                                        <div class="d-flex justify-start gap-x-4"
                                             :class="selectedItem.id === item.id ? 'selected' : ''">
                                            <icon-with-tooltip
                                                v-if="false"
                                                @click="showLiveVideoFeedModal(item)"
                                                :image="getIcon('video')"
                                                tooltipType="camera"
                                            ></icon-with-tooltip>
                                            <icon-with-tooltip
                                                v-if="canEdit('vehicle')"
                                                @click="editVehicle(item)"
                                                :image="getIcon('edit')"
                                                tooltipType="vehicleEdit"
                                            ></icon-with-tooltip>
                                            <icon-with-tooltip
                                                v-if="canDelete('vehicle')"
                                                @click="deleteVehicle(item)"
                                                :image="getIcon('delete')"
                                                tooltipType="vehicleRemove"
                                            ></icon-with-tooltip>
                                            <icon-with-tooltip
                                                @click="viewVehicle(item)"
                                                :image="getIcon('view')"
                                                tooltipType="vehicleDetails"
                                            ></icon-with-tooltip>
                                        </div>
                                    </template>
                                </v-data-table-server>
                            </v-card-text>
                        </v-col>
                    </v-row>
                </div>
            </v-container>

            <VehicleEditModal :item="vehicleItem" :itemsModels=itemsModels :fleets="fleets" :Objects="Objects"
                              :devices="devices" @close="closeEditModal"/>
            <ConfirmDeletionModal :data="deleteObject" @confirm="confirmDeletion" @close="closeDeletionModal"/>
            <LiveVideoFeedModal v-if="videoFeedItem.show" :item="videoFeedItem" @close="closeLiveVideoFeedModal"/>
            <ImportItemsModal :show="showCsvUploadModal" @close="setShowCsvUploadModal"/>
            <DeviceIssuesModal v-if="vehicleIssuesModal.show" :data="vehicleIssuesModal"
                               @close="vehicleIssuesModal.show = false"/>
        </template>
    </DashboardLayout>
</template>

<style>
  tr:has(> td > div.selected) {
    background: #fff4d7;
  }
</style>

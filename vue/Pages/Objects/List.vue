<script>
import DeletionRejectedModal from "@/Components/Modal/DeletionRejectedModal.vue";
import ConfirmDeletionModal from "@/Components/Modal/ConfirmDeletionModal.vue";
import SiteEditModal from "@/Components/Modal/SiteEditModal.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import staticData from "@/mixins/staticData.js";
import SvgIcon from '@jamescoyle/vue-icon';
import permission from "@/mixins/permission.js";
import ImportObjectsModal from "@/Components/Modal/ImportObjectsModal.vue";
import fileHelper from "@/mixins/fileHelper.js";
import SiteHierarchyModal from "@/Components/Modal/SiteHierarchyModal.vue";
import ObjectsTable from "@/Components/Tables/ObjectsTable.vue";
import query from "@/mixins/query.js";

export default {
  components: {
      ObjectsTable,
      SiteHierarchyModal,
      ImportObjectsModal,
      DeletionRejectedModal,
      ConfirmDeletionModal,
      SiteEditModal,
      DashboardLayout,
      SvgIcon,
  },
  mixins: [staticData, permission, fileHelper, query],

  props: {
    Objects: Array,
    appName: String,
  },
  data() {
    return {
      itemsPerPage: 10,
      filters: {
          search: ''
      },
      deleteObject: {
        id: 0,
        title: 'Delete Site',
        text: '',
        show: false
      },
      deletionRejectedObject: {
        type: 'site',
        count: 0,
        show: false
      },
      siteItem: {
        show: false,
        title: null,
        company_id: null,
        address: null,
        notes: null,
        enabled: true,
      },
      showCsvUploadModal: false,
      editSiteHierarchy: false
    }
  },
  methods: {
    setShowCsvUploadModal(show) {
      this.showCsvUploadModal = show;
    },
    addSite() {
      this.siteItem.show = true;
    },
    editSite(item) {
      this.siteItem = {
        ...item,
        show: true
      };
    },
    deleteSite(item) {
      if (item.items.length > 0) {
        this.deletionRejectedObject.count = item.items.length;
        this.deletionRejectedObject.show = true;
      } else {
        this.deleteObject.id = item.id;
        this.deleteObject.show = true;
        this.deleteObject.text = item.title;
      }
    },
    confirmDeletion() {
      this.$inertia.delete(`Objects/${this.deleteObject.id}`, {
        onSuccess: (page) => {
          this.closeDeletionModal();
        }
      });
    },
    closeEditModal() {
      this.siteItem = {
        title: '',
        address: '',
        notes: '',
        enabled: true,
        show: false
      };
    },
    closeDeletionModal() {
      this.deleteObject.id = 0;
      this.deleteObject.show = false;
    },
    closeRejectedModal() {
      this.deletionRejectedObject.show = false;
    },

    exportObjects() {
        this.downloadCsv('/Objects/export', this.generateCsvFileName('Objects'))
    },

    setEditSiteHierarchy(show) {
        this.editSiteHierarchy = show;
    },
    handleFilterChange() {
        const queryParts = [
            this.makeFilterString(this.filters)
        ].filter(Boolean);

        this.$inertia.get(`Objects?${queryParts.join('&')}`, {}, {
            preserveState: true,
            replace: true,
        });
    }

  }
}
</script>

<template>
  <DashboardLayout>
    <template #pageName>Objects</template>
    <template #default>
      <v-container>
        <div class="bg-white bordered rounded-2xl mt-10 py-3 px-3">
          <v-row>
            <v-col cols="3">
                <v-text-field
                    v-model="filters.search"
                    variant="outlined"
                    :append-inner-icon="'mdi-magnify'"
                    :hide-details="true"
                    class="ml-6 search-field"
                    placeholder="Type project code"
                    @click:append-inner="handleFilterChange"
                ></v-text-field>
            </v-col>
            <v-col class="text-right mx-4">
              <v-btn v-if="canCreate('site')" class="rounded-10 bordered" height="40" @click="addSite" color="amber-lighten-1">
                <span class="hidden-xs-only no-upper">Add site</span>
                <svg-icon type="mdi" :path="getIcon('plus')" class="ml-2" size="20px"></svg-icon>
              </v-btn>
              <v-btn v-if="canImportCsv('site')" class="rounded-10 bordered ml-4" height="40" @click="setShowCsvUploadModal(true)" color="amber-lighten-1">
                <span class="hidden-xs-only no-upper">import from csv</span>
                <svg-icon type="mdi" :path="getIcon('plus')"></svg-icon>
              </v-btn>

              <v-btn v-if="canExportCsv('site')" class="rounded-10 bordered ml-4" height="40" @click="exportObjects" color="amber-lighten-1">
                <span class="hidden-xs-only no-upper">export to csv</span>
                <svg-icon type="mdi" :path="getIcon('plus')"></svg-icon>
              </v-btn>

              <v-btn
                    class="ml-4"
                    height="40"
                    @click="setEditSiteHierarchy(true)"
                    color="amber-lighten-1"
              >
                <span class="hidden-xs-only no-upper">Change Hierarchy</span>
                <svg-icon type="mdi" :path="getIcon('edit')" class="ml-2" size="20px"></svg-icon>
              </v-btn>
            </v-col>
          </v-row>

          <v-row>
            <v-col>
                  <ObjectsTable
                    :items="Objects"
                    :onEdit="editSite"
                    :onDelete="deleteSite"
                  />
            </v-col>
          </v-row>
        </div>
      </v-container>

      <SiteEditModal :item="siteItem" @close="closeEditModal"/>
      <ConfirmDeletionModal :data="deleteObject" @confirm="confirmDeletion" @close="closeDeletionModal"/>
      <DeletionRejectedModal :data="deletionRejectedObject" @close="closeRejectedModal"/>
      <ImportObjectsModal :show="showCsvUploadModal" @close="setShowCsvUploadModal(false)"/>
      <SiteHierarchyModal :show="editSiteHierarchy" :Objects="Objects" @close="setEditSiteHierarchy(false)"/>
    </template>
  </DashboardLayout>

</template>

<style lang="scss">
.search-field {
    input.v-field__input {
        padding: 8px;
        min-height: auto;
    }
    max-width: 200px;
}
</style>

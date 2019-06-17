<template>
  <v-layout column>
    <v-flex>
      <RoleList/>
    </v-flex>
  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex"
  import RoleList from "~/components/RoleList"
  export default {
    name: "Roles",
    computed: {
      ...mapGetters({
        permissions: "init/PERMISSIONS"
      })
    },
    components: {RoleList},
    beforeRouteEnter(from, to, next) {
      next(vm => {
        if(!vm.permissions.includes('role_list')) {
          vm.$router.push('/400')
        }
      })
    }
  }
</script>

<style scoped>

</style>

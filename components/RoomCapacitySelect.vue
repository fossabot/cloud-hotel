<template>
  <v-select
    :loading="loading"
    v-bind="$attrs"
    :items="roomCapacity"
    item-text="name"
    item-value="id"
    @change="e => $emit('input', e)"
  />
</template>

<script>
  export default {
    name: "RoomCapacitySelect",
    data: () => ({
      roomCapacity: [],
      loading: false,
    }),
    async created() {
      try {
        this.loading = true;
        let url = '/room-capacities'
        const {data:{results}} = await this.$axios.get(url);
        this.roomCapacity = results;
      } finally {
        this.loading = false
      }
    }
  }
</script>

<style scoped>

</style>

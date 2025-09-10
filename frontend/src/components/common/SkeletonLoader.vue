<template>
  <div class="skeleton-loader" :style="containerStyle">
    <!-- Card Skeleton -->
    <div v-if="type === 'card'" class="skeleton-card" :style="cardStyle">
      <div class="skeleton-header">
        <div class="skeleton-avatar" :style="avatarStyle"></div>
        <div class="skeleton-text-group">
          <div class="skeleton-line" :style="titleStyle"></div>
          <div class="skeleton-line" :style="subtitleStyle"></div>
        </div>
      </div>
      <div class="skeleton-body">
        <div class="skeleton-line" :style="lineStyle" v-for="n in lines" :key="n"></div>
      </div>
      <div class="skeleton-footer" v-if="showFooter">
        <div class="skeleton-button" :style="buttonStyle"></div>
        <div class="skeleton-button" :style="buttonStyle"></div>
      </div>
    </div>

    <!-- Table Skeleton -->
    <div v-else-if="type === 'table'" class="skeleton-table">
      <div class="skeleton-table-header" :style="tableHeaderStyle">
        <div class="skeleton-cell" :style="cellStyle" v-for="n in columns" :key="`header-${n}`"></div>
      </div>
      <div class="skeleton-table-row" :style="tableRowStyle" v-for="r in rows" :key="`row-${r}`">
        <div class="skeleton-cell" :style="cellStyle" v-for="n in columns" :key="`cell-${r}-${n}`"></div>
      </div>
    </div>

    <!-- List Skeleton -->
    <div v-else-if="type === 'list'" class="skeleton-list">
      <div class="skeleton-list-item" :style="listItemStyle" v-for="n in items" :key="`item-${n}`">
        <div class="skeleton-avatar" :style="smallAvatarStyle"></div>
        <div class="skeleton-text-group">
          <div class="skeleton-line" :style="listTitleStyle"></div>
          <div class="skeleton-line" :style="listSubtitleStyle"></div>
        </div>
        <div class="skeleton-actions">
          <div class="skeleton-button" :style="smallButtonStyle"></div>
        </div>
      </div>
    </div>

    <!-- Text Skeleton -->
    <div v-else-if="type === 'text'" class="skeleton-text">
      <div class="skeleton-line" :style="textLineStyle" v-for="n in lines" :key="`text-${n}`"></div>
    </div>

    <!-- Chart Skeleton -->
    <div v-else-if="type === 'chart'" class="skeleton-chart" :style="chartStyle">
      <div class="skeleton-chart-header">
        <div class="skeleton-line" :style="chartTitleStyle"></div>
        <div class="skeleton-line" :style="chartSubtitleStyle"></div>
      </div>
      <div class="skeleton-chart-body">
        <div class="skeleton-bars">
          <div 
            class="skeleton-bar" 
            :style="getBarStyle(n)" 
            v-for="n in 6" 
            :key="`bar-${n}`"
          ></div>
        </div>
      </div>
    </div>

    <!-- Dashboard Stats Skeleton -->
    <div v-else-if="type === 'stats'" class="skeleton-stats">
      <div class="skeleton-stat-card" :style="statCardStyle" v-for="n in 4" :key="`stat-${n}`">
        <div class="skeleton-stat-icon" :style="statIconStyle"></div>
        <div class="skeleton-stat-content">
          <div class="skeleton-line" :style="statNumberStyle"></div>
          <div class="skeleton-line" :style="statLabelStyle"></div>
        </div>
      </div>
    </div>

    <!-- Custom Skeleton -->
    <div v-else class="skeleton-custom">
      <div class="skeleton-line" :style="lineStyle" v-for="n in lines" :key="`custom-${n}`"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SkeletonLoader',
  props: {
    type: {
      type: String,
      default: 'text',
      validator: (value) => ['card', 'table', 'list', 'text', 'chart', 'stats'].includes(value)
    },
    lines: {
      type: Number,
      default: 3
    },
    columns: {
      type: Number,
      default: 4
    },
    rows: {
      type: Number,
      default: 5
    },
    items: {
      type: Number,
      default: 5
    },
    showFooter: {
      type: Boolean,
      default: true
    },
    height: {
      type: String,
      default: 'auto'
    },
    width: {
      type: String,
      default: '100%'
    }
  },
  computed: {
    containerStyle() {
      return {
        width: this.width,
        height: this.height
      }
    },
    baseSkeletonStyle() {
      return {
        background: 'linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%)',
        backgroundSize: '200% 100%',
        animation: 'shimmer 1.5s infinite',
        borderRadius: '4px'
      }
    },
    cardStyle() {
      return {
        ...this.baseSkeletonStyle,
        padding: '20px',
        borderRadius: '12px',
        backgroundColor: '#f8f9fa',
        background: 'white',
        boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)'
      }
    },
    avatarStyle() {
      return {
        ...this.baseSkeletonStyle,
        width: '50px',
        height: '50px',
        borderRadius: '50%',
        marginRight: '16px',
        flexShrink: '0'
      }
    },
    smallAvatarStyle() {
      return {
        ...this.baseSkeletonStyle,
        width: '40px',
        height: '40px',
        borderRadius: '50%',
        marginRight: '12px',
        flexShrink: '0'
      }
    },
    titleStyle() {
      return {
        ...this.baseSkeletonStyle,
        height: '20px',
        width: '200px',
        marginBottom: '8px'
      }
    },
    subtitleStyle() {
      return {
        ...this.baseSkeletonStyle,
        height: '16px',
        width: '150px'
      }
    },
    lineStyle() {
      return {
        ...this.baseSkeletonStyle,
        height: '16px',
        width: '100%',
        marginBottom: '12px'
      }
    },
    buttonStyle() {
      return {
        ...this.baseSkeletonStyle,
        height: '36px',
        width: '100px',
        borderRadius: '6px',
        marginRight: '12px'
      }
    },
    smallButtonStyle() {
      return {
        ...this.baseSkeletonStyle,
        height: '32px',
        width: '80px',
        borderRadius: '6px'
      }
    },
    tableHeaderStyle() {
      return {
        display: 'flex',
        gap: '16px',
        marginBottom: '16px',
        padding: '12px 0',
        borderBottom: '1px solid #e5e7eb'
      }
    },
    tableRowStyle() {
      return {
        display: 'flex',
        gap: '16px',
        marginBottom: '12px',
        padding: '12px 0'
      }
    },
    cellStyle() {
      return {
        ...this.baseSkeletonStyle,
        height: '20px',
        flex: '1'
      }
    },
    listItemStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        padding: '16px',
        marginBottom: '8px',
        backgroundColor: 'white',
        borderRadius: '8px',
        boxShadow: '0 1px 3px rgba(0, 0, 0, 0.1)'
      }
    },
    listTitleStyle() {
      return {
        ...this.baseSkeletonStyle,
        height: '18px',
        width: '180px',
        marginBottom: '6px'
      }
    },
    listSubtitleStyle() {
      return {
        ...this.baseSkeletonStyle,
        height: '14px',
        width: '120px'
      }
    },
    textLineStyle() {
      return {
        ...this.baseSkeletonStyle,
        height: '16px',
        width: `${Math.random() * 40 + 60}%`,
        marginBottom: '12px'
      }
    },
    chartStyle() {
      return {
        padding: '20px',
        backgroundColor: 'white',
        borderRadius: '12px',
        boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)'
      }
    },
    chartTitleStyle() {
      return {
        ...this.baseSkeletonStyle,
        height: '24px',
        width: '200px',
        marginBottom: '8px'
      }
    },
    chartSubtitleStyle() {
      return {
        ...this.baseSkeletonStyle,
        height: '16px',
        width: '150px',
        marginBottom: '24px'
      }
    },
    statCardStyle() {
      return {
        display: 'flex',
        alignItems: 'center',
        padding: '20px',
        backgroundColor: 'white',
        borderRadius: '12px',
        boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)',
        marginBottom: '16px'
      }
    },
    statIconStyle() {
      return {
        ...this.baseSkeletonStyle,
        width: '48px',
        height: '48px',
        borderRadius: '12px',
        marginRight: '16px'
      }
    },
    statNumberStyle() {
      return {
        ...this.baseSkeletonStyle,
        height: '32px',
        width: '80px',
        marginBottom: '8px'
      }
    },
    statLabelStyle() {
      return {
        ...this.baseSkeletonStyle,
        height: '16px',
        width: '100px'
      }
    }
  },
  methods: {
    getBarStyle(index) {
      const heights = [60, 80, 40, 90, 70, 50]
      return {
        ...this.baseSkeletonStyle,
        width: '40px',
        height: `${heights[index - 1]}px`,
        marginRight: '12px',
        borderRadius: '4px 4px 0 0'
      }
    }
  }
}
</script>

<style scoped>
.skeleton-loader {
  width: 100%;
}

.skeleton-header {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.skeleton-text-group {
  flex: 1;
}

.skeleton-body {
  margin-bottom: 20px;
}

.skeleton-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}

.skeleton-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.skeleton-stat-content {
  flex: 1;
}

.skeleton-chart-body {
  height: 200px;
  display: flex;
  align-items: flex-end;
  justify-content: center;
}

.skeleton-bars {
  display: flex;
  align-items: flex-end;
  justify-content: center;
  height: 100%;
  width: 100%;
}

.skeleton-actions {
  margin-left: auto;
}

/* Shimmer Animation */
@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

/* Responsive */
@media (max-width: 768px) {
  .skeleton-stats {
    grid-template-columns: 1fr;
  }
  
  .skeleton-stat-card {
    margin-bottom: 12px;
  }
  
  .skeleton-table-header,
  .skeleton-table-row {
    flex-direction: column;
    gap: 8px;
  }
}
</style>

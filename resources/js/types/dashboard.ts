interface DashboardTotals {
  total_income: string
  total_expense: string
}

interface TotalsByCategoryRow {
  category_id: string
  category_name: string
  category_color: string
  type: string
  total: string
}

interface DashboardHistoryItem {
  period: string
  label: string
  income: number
  expense: number
}

export type { DashboardHistoryItem, DashboardTotals, TotalsByCategoryRow }

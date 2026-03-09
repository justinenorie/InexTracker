type CategoryType = 'income' | 'expense' | 'both'

interface Category {
  id: string
  name: string
  type: CategoryType | string
  color?: string | null
  deleted_at?: string | null
  created_at?: string
  updated_at?: string
}

export type { Category, CategoryType }

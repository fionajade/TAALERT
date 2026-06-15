import { createClient } from '@supabase/supabase-js'

const supabaseUrl = "https://okwnknzodfvbsbgetvzh.supabase.co"
const supabaseAnonKey = "sb_publishable_isCj681TK4luzYBQWSNJUg_qhCavuBs"

export const supabase = createClient(supabaseUrl, supabaseAnonKey)
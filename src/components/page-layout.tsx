import { PageHeader } from "./page-header";

interface PageLayoutProps {
  children: React.ReactNode;
  title?: string;
}

export function PageLayout({ children, title }: PageLayoutProps) {
  return (
    <div className="flex flex-col min-h-screen">
      <PageHeader title={title} />
      <main className="flex-1 flex flex-col items-center gap-8 max-w-5xl mx-auto p-8 sm:p-20 bg-gradient-to-b from-gray-50 to-white dark:from-gray-950 dark:to-gray-900">
        {children}
      </main>
    </div>
  );
}

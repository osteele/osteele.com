import Link from "next/link";
import { FaExternalLinkAlt } from "react-icons/fa";

interface ResourceCardProps {
  title: string;
  description: string;
  href: string;
}

export const ResourceCard = ({ title, description, href }: ResourceCardProps) => (
  <Link href={href} className="block">
    <div className="relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 shadow-md hover:shadow-lg transition-all group">
      <div className="absolute inset-0 bg-gradient-to-br from-white from-40% via-[#FF6B4A]/5 via-60% to-[#FF6B4A]/10 dark:from-gray-800 dark:via-[#FF8A6B]/5 dark:to-[#FF8A6B]/20" />
      <div className="relative p-4">
        <div className="flex items-center gap-2 mb-2">
          <h3 className="text-xl group-hover:text-[#FF6B4A] dark:group-hover:text-[#FF8A6B] transition-colors">
            {title}
          </h3>
          <FaExternalLinkAlt className="h-4 w-4 text-gray-400 group-hover:text-[#FF6B4A] dark:group-hover:text-[#FF8A6B] transition-colors" />
        </div>
        <p className="text-gray-600 dark:text-gray-300">{description}</p>
      </div>
    </div>
  </Link>
);

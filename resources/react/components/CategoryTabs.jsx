import React from 'react';

const CategoryTabs = ({ categories, activeCategory, onSelect }) => {
    if (!categories || categories.length === 0) return null;

    return (
        <div className="flex overflow-x-auto hide-scrollbar py-4 gap-3 sticky top-16 z-30 bg-bg/90 backdrop-blur-md">
            <button
                onClick={() => onSelect(null)}
                className={`flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all ${
                    activeCategory === null
                        ? 'bg-brand text-white shadow-glow'
                        : 'bg-surface text-muted border border-subtle hover:text-white'
                }`}
            >
                Semua Menu
            </button>
            {categories.map((cat) => (
                <button
                    key={cat.id}
                    onClick={() => onSelect(cat.slug)}
                    className={`flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all flex items-center gap-2 ${
                        activeCategory === cat.slug
                            ? 'bg-brand text-white shadow-glow'
                            : 'bg-surface text-muted border border-subtle hover:text-white'
                    }`}
                >
                    <span>{cat.icon}</span>
                    {cat.name}
                </button>
            ))}
        </div>
    );
};

export default CategoryTabs;

"""یک درخت تک‌تقسیمی آموزشی با جینیِ وزن‌دار."""
from collections import Counter
from math import log2
import json


def gini(labels):
    if not labels:
        return 0
    return 1 - sum((n / len(labels)) ** 2 for n in Counter(labels).values())


def entropy(labels):
    if not labels:
        return 0
    return -sum((n / len(labels)) * log2(n / len(labels))
                for n in Counter(labels).values())


def best_split(features, labels):
    values = sorted(set(features))
    options = []
    for a, b in zip(values, values[1:]):
        threshold = (a + b) / 2
        left = [y for x, y in zip(features, labels) if x <= threshold]
        right = [y for x, y in zip(features, labels) if x > threshold]
        cost = (len(left) * gini(left) + len(right) * gini(right)) / len(labels)
        options.append((cost, threshold))
    if not options:
        raise ValueError("At least two distinct feature values are required")
    return min(options)


def run_demo():
    cost, threshold = best_split([1, 2, 3, 4, 5, 6], [0, 0, 0, 1, 1, 1])
    assert cost == 0 and threshold == 3.5
    weighted_example = (2 * gini([0, 0]) + 4 * gini([0, 0, 1, 1])) / 6
    assert abs(weighted_example - 1/3) < 1e-12
    assert entropy([1, 1]) == 0
    return {"best_threshold": threshold, "split_cost": cost,
            "weighted_example": weighted_example}


if __name__ == "__main__":
    print(json.dumps(run_demo(), ensure_ascii=False))
